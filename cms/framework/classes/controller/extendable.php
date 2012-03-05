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

class Controller_Extendable extends \Controller {
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
        $ret = \Config::load(APPPATH.'data'.DS.'config'.DS.'modules_dependencies.php', true);
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

		$translatable  = $model::observers('Cms\Orm_Translatable');
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
				foreach ($config['dataset'] as $key => $data) {
					if (is_array($data)) {
						$data = $data['value'];
					}
					if (is_callable($data)) {
						$item[$key] = $data($object);
					} else {
						$item[$key] = $object->{$data};
					}
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
				$langs = Orm_Translatable::orm_notify_class($model, 'languages', $common_ids);
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
			'items' => $items,
			'total' => $count,
		);
	}
}
