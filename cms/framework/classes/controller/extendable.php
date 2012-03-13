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

class Controller_Extendable extends \Fuel\Core\Controller {
    protected $config = array();

    public function before() {
        $this->config = \Arr::merge($this->config, $this->getConfiguration());
        $this->trigger('before', $this, 'boolean');
    }

    public function after($response) {
        if (isset($this->config['assets'])) {
            if (isset($this->config['assets']['paths'])) {
                foreach ($this->config['assets']['paths'] as $path) {
                    \Asset::add_path($path);
                }
            }
            if (isset($this->config['assets']['css'])) {
                foreach ($this->config['assets']['css'] as $css) {
                    \Asset::css($css, array(), 'css');
                }
            }
            if (isset($this->config['assets']['js'])) {
                foreach ($this->config['assets']['js'] as $js) {
                    \Asset::js($js, array(), 'js');
                }
            }
        }
        return parent::after($response);
    }

    protected function trigger($event, $data = '', $return_type = 'string') {
        list($module_name, $file_name) = $this->getLocation();
        $file_name = str_replace('/', '_', $file_name);
        return \Event::trigger($module_name.'.'.$file_name.'.'.$event, $data, $return_type);
    }

    protected static function getConfiguration() {
        list($module_name, $file_name) = self::getLocation();
        return static::loadConfiguration($module_name, $file_name);
    }

    protected static function getLocation() {
        // @todo use get_called_class() instead
        $controller = explode('\\', \Request::active()->controller);
        $module_name = strtolower($controller[0]);
        $file_name   = strtolower(str_replace('_', DS, $controller[1]));
        $location = array($module_name, $file_name);
        if ($module_name == 'cms') {
            $submodule = explode('_', $controller[1]);
            if ($submodule[0] == 'Controller' && $submodule[1] == 'Admin' && count($submodule) > 2) {
                $location[] = strtolower($submodule[2]);
            }
        }

        return $location;
    }

    protected static function loadConfiguration($module_name, $file_name) {
        \Config::load($module_name.'::'.$file_name, true);
        $config = \Config::get($module_name.'::'.$file_name);
        \Config::load(APPPATH.'data'.DS.'config'.DS.'modules_dependencies.php', true);
        $dependencies = \Config::get(APPPATH.'data'.DS.'config'.DS.'modules_dependencies.php', array());
        if (!empty($dependencies[$module_name])) {
            foreach ($dependencies[$module_name] as $dependency) {
                \Config::load($dependency.'::'.$file_name, true);
                $config = \Arr::merge($config, \Config::get($dependency.'::'.$file_name));
            }
        }
        $config = \Arr::recursive_filter($config, function($var) { return $var !== null; });
        return $config;
    }

    protected static function check_permission_action($action, $dataset_location, $item = null) {
        \Config::load($dataset_location, true);
        $dataset = \Config::get($dataset_location.'.dataset');
        // An unknown action is authorized
        // This is for consistency with client-side, where actions are visible by default (not greyed out)
        if (empty($dataset['actions']) || empty($dataset['actions'][$action])) {
            return true;
        }
        return $dataset['actions'][$action]($item);
    }
    protected function items(array $config, $only_count = false)
    {
        $config = array_merge(array(
            'related' => array(),
            'callback' => array(),
            'lang' => null,
            'limit' => null,
            'offset' => null,
            'dataset' => array(),
        ), $config);

        $items = array();

        $model = $config['model'];
        $pk = \Arr::get($model::primary_key(), 0);

        $query = \Cms\Orm\Query::forge($model, $model::connection());
        foreach ($config['related'] as $related) {
            $query->related($related);
        }

        foreach ($config['callback'] as $callback) {
            if (is_callable($callback)) {
                $query = $callback($query);
            }
        }

	    if (!empty($config['order_by'])) {
		    $orders_by = $config['order_by'];
		    if (!is_array($orders_by)) {
			    $orders_by = array($orders_by);
		    }
		    foreach ($orders_by as $order_by => $direction) {
			    if (!is_string($order_by)) {
				    $order_by = $direction;
				    $direction = 'ASC';
			    }
			    $query->order_by($order_by, $direction);
		    }
	    }

        $translatable  = $model::behaviors('Cms\Orm_Behaviour_Translatable');
        if ($translatable) {
            if (empty($config['lang'])) {
                // No inspector, we only search items in their primary language
                $query->where($translatable['single_id_property'], 'IS NOT', null);
            } else if (is_array($config['lang'])) {
                // Multiple langs
                $query->where($translatable['lang_property'], 'IN', $config['lang']);
            } else  {
                $query->where($translatable['lang_property'],  '=', $config['lang']);
            }
            $common_ids = array();
            $keys = array();
        }
        $count = $query->count();
        if ($only_count) {
            return array(
                'query' => (string) $query->get_query(),
                'query2' => '',
                'items' => array(),
                'total' => $count,
            );
        }

        // Copied over and adapted from $query->count()
        $select = \Arr::get($model::primary_key(), 0);
        $select = (strpos($select, '.') === false ? $query->alias().'.'.$select : $select);
        // Get the columns
        $columns = \DB::expr('DISTINCT '.\Database_Connection::instance()->quote_identifier($select).' AS group_by_pk');
        // Remove the current select and
        $new_query = call_user_func('DB::select', $columns);
        // Set from table
        $new_query->from(array($model::table(), $query->alias()));

        $tmp   = $query->build_query($new_query, $columns, 'select');
        $new_query = $tmp['query'];
        $new_query->group_by('group_by_pk');
        if ($config['limit']) {
            $new_query->limit($config['limit']);
        }
        if ($config['offset']) {
            $new_query->offset($config['offset']);
        }
        $objects = $new_query->execute($query->connection())->as_array('group_by_pk');

        if (!empty($objects)) {
            $query = $model::find()->where(array($select, 'in', array_keys($objects)));
            foreach ($config['related'] as $related) {
                $query->related($related);
            }
            foreach ($config['callback'] as $callback) {
                if (is_callable($callback)) {
                    $query = $callback($query);
                }
            }

            foreach ($query->get() as $object) {
                $item = array();
	            $dataset = $config['dataset'];
	            $actions = \Arr::get($dataset, 'actions', array());
	            unset($dataset['actions']);
                $object->import_dataset_behaviours($dataset);
                foreach ($dataset as $key => $data) {
                    if (is_callable($data)) {
                        $item[$key] = call_user_func($data, $object);
                    } else if (is_array($data)) {
                        $data = $data['value'];
                    } else {
                        $item[$key] = $object->{$data};
                    }
                }
	            $item['actions'] = array();
	            foreach ($actions as $action => $callback) {
		            $item['actions'][$action] = $callback($object);
	            }
                $item['_id'] = $object->{$pk};
                $item['_model'] = $model;
                $items[] = $item;
                if ($translatable) {
                    $common_id = $object->{$translatable['common_id_property']};
                    $keys[] = $common_id;
                    $common_ids[$translatable['common_id_property']][] = $common_id;
                }
            }
            if ($translatable) {
	            $langs = $model::languages($common_ids);
                foreach ($keys as $key => $common_id) {
                    $items[$key]['lang'] = $langs[$common_id];
                }

                foreach ($items as &$item) {
                    $flags = '';
                    foreach (explode(',', $item['lang']) as $lang) {
                        // Convert lang_LOCALE to locale
                        list($lang, $locale) = explode('_', $lang.'_');
                        if (!empty($locale)) {
                            $lang = strtolower($locale);
                        }
                        switch($lang) {
                            case 'en':
                                $lang = 'gb';
                                break;
                        }
                        $flags .= '<img src="static/cms/img/flags/'.$lang.'.png" /> ';
                    }
                    $item['lang'] = $flags;
                }
            }
        }

		return array(
			'query' => (string) $query->get_query(),
			'query2' => (string) $new_query->compile(),
			'offset' => $config['offset'],
			'limit' => $config['limit'],
			'items' => $items,
			'total' => $count,
		);
	}

	protected function build_tree($tree) {
		$list_models  = array();
		foreach ($tree['models'] as $model) {
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
        $parents = array();
		foreach ($list_models as $model) {
            foreach ($model['childs'] as $child) {
                $parents[$child['model']][] = $model['model'];
            }
        }
        $tree['parents'] = $parents;
		$tree['models'] = $list_models;

		$list_roots = array();
		if (!is_array($tree['roots'])) {
			$tree['roots'] = array($tree['roots']);
		}
		foreach ($tree['roots'] as $root) {
			if (!is_array($root)) {
				$root = array('model' => $root);
			}
			if (!isset($root['where']) || !is_array($root['where'])) {
				$root['where'] = array();
			}
			if (isset($tree['models'][$root['model']])) {
				$list_roots[] = $root;
			}
		}
		$tree['roots'] = $list_roots;

		return $tree;
	}

	protected function tree(array $tree_config)
	{
		$id = \Input::get('id');
		$model = \Input::get('model');
		$deep = intval(\Input::get('deep', 1));
		$lang = \Input::get('lang');

        if (empty($tree_config['id'])) {
            $tree_config['id'] = \Config::getBDDName(join('::', $this->getLocation()));
        }

		$tree_config = $this->build_tree($tree_config);

		if ($deep === -1) {
			\Session::set('tree.'.$tree_config['id'].'.'.$model.'|'.$id, false);
			$count = $this->tree_items($tree_config, array(
				'countProcess' => true,
				'model' => $model,
				'id' => $id,
				'lang' => $lang,
			));

			$json = array(
				'items' => array(),
				'total' => $count,
			);
		} else {
			\Session::set('tree.'.$tree_config['id'].'.'.$model.'|'.$id, true);
			$items = $this->tree_items($tree_config, array(
				'model' => $model,
				'id' => $id,
				'deep' => $deep,
				'lang' => $lang,
			));

			$json = array(
				'items' => $items,
				'total' => count($items),
			);
		}
		return $json;
	}

	public function tree_items(array $tree_config, array $params)
	{
		$params = array_merge(array(
			'countProcess' => false,
			'model' => null,
			'id' => null,
			'deep' => 1,
			'lang' => null,
		), $params);

		$childs = array();
		if (!$params['model']) {
			$childs = $tree_config['roots'];
		} else {
			$tree_model = $tree_config['models'][$params['model']];
			foreach ($tree_model['childs'] as $child) {
				$child['where'] = array(array($child['fk'] => $params['id']));
				$childs[]       = $child;
			}
		}

		$items = array();
		$count = 0;
		foreach ($childs as $child) {
			$tree_model = $tree_config['models'][$child['model']];
			$pk = $tree_model['pk'];
			$controler = $this;

			$config = array_merge($tree_model, array(
				'lang' => $params['lang'],
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
					'treeChilds' => function($object) use ($controler, $tree_config, $params, $child, $pk) {
						if ($params['deep'] > 1 && \Session::get('tree.'.$tree_config['id'].'.'.$child['model'].'|'.$object->{$pk}, true)) {
							$items = $controler->tree_items($tree_config, array(
								'model' => $child['model'],
								'id' => $object->{$pk},
								'deep' => $params['deep'] - 1,
								'lang' => $params['lang'],
							));
							return count($items) ? $items : 0;
						} else {
							return $controler->tree_items($tree_config, array(
								'countProcess' => true,
								'model' => $child['model'],
								'id' => $object->{$pk},
								'lang' => $params['lang'],
							));
						}
					},
				)),
			));

			if ($params['countProcess']) {
				$return = $this->items($config, true);
				$count += $return['total'];
			} else {
				$return = $this->items($config);
				$items = array_merge($items, $return['items']);
			}
		}
		return $params['countProcess'] ? $count : $items;
	}
}
