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
	        'limit' => intval(Input::get('limit', \Arr::get($this->mp3grid['query'], 'limit'))),
	        'offset' => intval(Input::get('offset', 0)),
	    )));

        $json = array(
            'get' => '',
            'query' =>  '',
	        'query2' =>  '',
            'offset' => $return['offset'],
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

		$json = $this->tree(array_merge(array('id' => $this->mp3grid['configuration_id']), $this->mp3grid['tree']));

		if (\Fuel::$env === \Fuel::DEVELOPMENT) {
			$json['get'] = Input::get();
		}
		if (\Input::get('debug') !== null) {
			\Debug::dump($json);
			exit();
		}

		\Response::json($json);
	}
}

/* End of file list.php */
