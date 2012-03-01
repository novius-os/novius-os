<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

use Fuel\Core\Request;

use Asset, Format, Input, Session, View, Uri;

/**
 * The cloud Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Mp3table_List extends Controller_Extendable {

	protected $mp3grid = array();

    public function before() {
        parent::before();
        if (!isset($this->config['mp3grid'])) {
            list($module_name, $file_name) = $this->getLocation();
            $file_name = explode('/', $file_name);
            array_splice($file_name, count($file_name) - 1, 0, array('mp3grid'));
            $file_name = implode('/', $file_name);
        } else {
            list($module_name, $file_name) = explode('::', $this->config['mp3grid']);
        }

		$this->mp3grid = \Config::mergeWithUser($module_name.'::'.$file_name, static::loadConfiguration($module_name, $file_name));
    }

	public function action_index($view = null, $delayed = false) {
		if (!\Cms\Auth::check()) {
			\Response::redirect('/admin/login?redirect='.urlencode($_SERVER['REDIRECT_URL']));
			exit();
		}

        if (empty($view)) {
            $view = \Input::get('view', $this->mp3grid['selectedView']);
        }
        $this->mp3grid['selectedView'] = $view;

        if (empty($this->mp3grid['custom'])) {
            $this->mp3grid['custom'] = array(
                'from' => 'default',
            );
        }

		$view = View::forge('mp3table/list');

        if ($delayed) {
            $this->mp3grid['delayed'] = true;
        }


        /*
        $view->set('urljson', $this->mp3grid['views'][$this->mp3grid['selectedView']]['json'], false);
		$view->set('i18n', \Format::forge($this->mp3grid['i18n'])->to_json(), false);
        $view->set('views', \Format::forge($this->mp3grid['views'])->to_json(), false);
        $view->set('selectedView', \Format::forge($this->mp3grid['selectedView'])->to_json(), false);
        $view->set('name', \Format::forge($this->mp3grid['configuration_id'])->to_json(), false);
         */
        //\Debug::dump($this->mp3grid);
        $view->set('mp3grid', \Format::forge($this->mp3grid)->to_json(), false);
		return $view;
	}

    public function action_delayed($view = null) {
        return $this->action_index($view, true);
    }

    public function action_json()
    {

		if (!\Cms\Auth::check()) {
			\Response::json(403, array(
				'login_page' => \Uri::base(false).'admin/login',
			));
		}

	    $offset = intval(Input::get('offset', 0));
	    $limit = intval(Input::get('limit', \Arr::get($this->mp3grid['query'], 'limit')));
	    $config = $this->mp3grid;
	    $where = function($query) use ($config) {
		    foreach ($config['inputs'] as $input => $condition) {
			    $value = Input::get('inspectors.'.$input);
			    if (is_callable($condition)) {
				    $query = $condition($value, $query);
			    }
		    }

		    Filter::apply($query, $config);

		    return $query;
	    };

	    $return = $this->items(array_merge($this->mp3grid['query'], array(
		    'callback' => array($where),
		    'dataset' => $this->mp3grid['dataset'],
		    'lang' => Input::get('inspectors.lang', null),
	        'limit' => $limit,
	        'offset' => $offset,
	    )));

        $json = array(
            'get' => '',
            'query' =>  '',
	        'query2' =>  '',
            'offset' => $offset,
            'items' => $return['items'],
            'total' => $return['total'],
        );

        if (\Fuel::$env === \Fuel::DEVELOPMENT) {
            $json['get'] = Input::get();
            $json['query'] = $return['query'];
	        $json['query2'] = $return['query2'];
        }
        if (\Input::get('debug') !== null) {
            \Debug::dump($json);
            exit();
        }

        \Response::json($json);
    }

	protected function searchtext_condition($menu, $target, $search)
	{
		if ($target) {
			if ($menu['target'] == $target) {
				if (isset($menu['column'])) {
					return array(array($menu['column'], 'like', '%'.$search.'%'));
				} else if (isset($menu['submenu']) && is_array($menu['submenu'])) {
					$wheres = array();
					foreach ($menu['submenu'] as $smenu) {
						$wheres = array_merge($wheres, $this->searchtext_condition($smenu, false, $search));
					}
					return $wheres;
				}
			} else if (isset($menu['submenu']) && is_array($menu['submenu'])) {
				foreach ($menu['submenu'] as $smenu) {
					$where = $this->searchtext_condition($smenu, $target, $search);
					if (count($where)) {
						return $where;
					}
				}
			}
		} else {
			if (isset($menu['column'])) {
				return array(array($menu['column'], 'like', '%'.$search.'%'));
			} else if (isset($menu['submenu']) && is_array($menu['submenu'])) {
				$wheres = array();
				foreach ($menu['submenu'] as $smenu) {
					$wheres = array_merge($wheres, $this->searchtext_condition($smenu, false, $search));
				}
				return $wheres;
			}
		}
		return array();
	}

	public function action_tree_json()
	{
		if (!\Cms\Auth::check()) {
			\Response::json(403, array(
				'login_page' => \Uri::base(false).'admin/login',
			));
		}

		$id = Input::get('id');
		$model = Input::get('model');
		$deep = intval(Input::get('deep', 1));
		$this->build_tree();
		if ($deep === -1) {
			\Session::set('tree.'.$this->mp3grid['configuration_id'].'.'.$model.'|'.$id, false);
			$count = $this->tree_items(true, $model, $id);

			$json = array(
				'get' => '',
				'items' => array(),
				'total' => $count,
			);
		} else {
			\Session::set('tree.'.$this->mp3grid['configuration_id'].'.'.$model.'|'.$id, true);
			$items = $this->tree_items(false, $model, $id, $deep);

			$json = array(
				'get' => '',
				'items' => $items,
				'total' => count($items),
			);
		}

		if (\Fuel::$env === \Fuel::DEVELOPMENT) {
			$json['get'] = Input::get();
		}
		if (\Input::get('debug') !== null) {
			\Debug::dump($json);
			exit();
		}

		\Response::json($json);
	}

	protected function build_tree() {
		$list_models  = array();
		foreach ($this->mp3grid['tree']['models'] as $model) {
			if (!is_array($model)) {
				$model = array('model' => $model);
			}
			$class = $model['model'];
			if (!isset($model['pk'])) {
				$model['pk'] = \Arr::get($class::primary_key(), 0);
			}
			if (!isset($model['order_by'])) {
				$model['order_by'] = array($model['pk']);
			} elseif (!is_array($model['order_by'])) {
				$model['order_by'] = array($model['order_by']);
			}
			if (!isset($model['childs'])) {
				$model['childs'] = array();
			}
			$list_models[$model['model']] = $model;
		}

		foreach ($list_models as $model) {
			$childs = array();
			foreach ($model['childs'] as $child) {
				if (!is_array($child)) {
					if (!isset($list_models[$child])) {
						continue;
					}
					$class     = $list_models[$child]['model'];
					$relations = $class::relations();
					foreach ($relations as $relation) {
						if ($relation->model_to == $model['model']) {
							$foreignkey = $relation->key_from;
							$childs[] = array(
								'relation'  => $relation->name,
								'model'      => $child,
								'fk'        => $foreignkey[0],
							);
							break;
						}
					}
				} else {
					if (isset($child['model']) && isset($child['fk'])) {
						$childs[] = $child;
					}
				}
			}
			$list_models[$model['model']]['childs'] = $childs;
		}
		$this->mp3grid['tree']['models'] = $list_models;

		$list_roots = array();
		if (!is_array($this->mp3grid['tree']['roots'])) {
			$this->mp3grid['tree']['roots'] = array($this->mp3grid['tree']['roots']);
		}
		foreach ($this->mp3grid['tree']['roots'] as $root) {
			if (!is_array($root)) {
				$root = array('model' => $root);
			}
			if (!isset($root['where']) || !is_array($root['where'])) {
				$root['where'] = array();
			}
			if (isset($this->mp3grid['tree']['models'][$root['model']])) {
				$list_roots[] = $root;
			}
		}
		$this->mp3grid['tree']['roots'] = $list_roots;
	}

	public function tree_items($countProcess = false, $model = null, $id = null, $deep = 1)
	{
		$childs = array();
		if (!$model) {
			$childs = $this->mp3grid['tree']['roots'];
		} else {
			$tree_model = $this->mp3grid['tree']['models'][$model];
			foreach ($tree_model['childs'] as $child) {
				$child['where'] = array(array($child['fk'] => $id));
				$childs[]       = $child;
			}
		}

		$items = array();
		$count = 0;
		foreach ($childs as $child) {
			$tree_model = $this->mp3grid['tree']['models'][$child['model']];
			$pk = $tree_model['pk'];
			$configuration_id = $this->mp3grid['configuration_id'];
			$controler = $this;

			$config = array_merge($tree_model, array(
				'callback' => array(function($query) use ($child, $tree_model) {
					foreach($child['where'] as $where) {
						$query->where($where);
					}
					foreach($tree_model['order_by'] as $order_by) {
						$query->order_by(is_array($order_by) ? $order_by : array($order_by));
					}
					return $query;
				}),
				'dataset' => array_merge($tree_model['dataset'], array(
					'treeChilds' => function($object) use ($controler, $deep, $configuration_id, $child, $pk) {
						if ($deep > 1 || \Session::get('tree.'.$configuration_id.'.'.$child['model'].'|'.$object->{$pk})) {
							return $controler->tree_items(false, $child['model'], $object->{$pk}, $deep - 1);
						} else {
							return $controler->tree_items(true, $child['model'], $object->{$pk});
						}
					},
				)),
			));

			if ($countProcess) {
				$return = $this->items($config, true);
				$count += $return['total'];
			} else {
				$return = $this->items($config);
				$items = array_merge($items, $return['items']);
			}
		}
		return $countProcess ? $count : $items;
	}
}

/* End of file list.php */
