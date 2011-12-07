<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Blog;

use Fuel\Core\Config;

use Cms\Controller_Mp3table_List;

use Asset, Format, Input, Session, View, Uri;

class Controller_Admin_List extends Controller_Mp3table_List {

	public function before() {
		Config::load('cms_blog::admin/blog', true);
		$this->config = Config::get('cms_blog::admin/blog', array());

		parent::before();
	}

	public function after($response) {
		\Asset::add_path('static/modules/cms_blog/');
		\Asset::css('admin.css', array(), 'css');
		return parent::after($response);
	}

    public function applySorting($query, $sorting) {
        for ($i = 0; $i < count($sorting); $i++) {
            $key = $sorting[$i]['dataKey'];
            if (is_array($this->config['dataset'][$key])) {
                if ($this->config['dataset'][$key]['search_relation']) {
                    $query->related($this->config['dataset'][$key]['search_relation']);
                }
                $column = $this->config['dataset'][$key]['search_column'];
            } else {
                $column = $this->config['dataset'][$key];
            }
            $query->order_by($column, $sorting[$i]['sortDirection'] == 'ascending' ? 'ASC' : 'DESC');
        }
    }

	public function action_json()
	{
		$offset = intval(Input::get('offset', 0));
		$limit = intval(Input::get('limit', $this->config['query']['limit']));
		// SORTING
        $sorting = Input::get('sorting', array());



		$items = array();

		$model = $this->config['query']['model'];

		$query = \Cms\Orm\Query::forge($model, $model::connection());
		foreach ($this->config['query']['related'] as $related) {
			$query->related($related);
		}

		foreach ($this->config['inputs'] as $input => $condition) {
			$value = Input::get('inspectors.'.$input);
			if (is_callable($condition)) {
				$query = $condition($value, $query);
			}
		}



		$inspectors_lang = Input::get('inspectors.lang', null);
		$translatable  = $model::observers('Cms\Orm_Translatable');
		if ($translatable) {

			if (empty($inspectors_lang)) {
				// No inspector, we only search items in their primary language
				$query->where($translatable['single_id_property'], 'IS NOT', null);
			} else if (is_array($inspectors_lang)) {
				// Multiple langs
				$query->where($translatable['lang_property'], 'IN', $inspectors_lang);
			} else  {
				$query->where($translatable['lang_property'],  '=', $inspectors_lang);
			}
			$common_ids = array();
			$keys = array();
		}

        // SORTING
        $this->applySorting($query, $sorting);

		$count = $query->count();


		// Copied over and adapted from $query->count()
		$select = $column ?: \Arr::get($model::primary_key(), 0);
		$select = (strpos($select, '.') === false ? $query->alias().'.'.$select : $select);

		// Get the columns
		$columns = \DB::expr('DISTINCT '.\Database_Connection::instance()->quote_identifier($select).' AS group_by_pk');

		// Remove the current select and
		$new_query = call_user_func('DB::select', $columns);

		// Set from table
		$new_query->from(array($model::table(), $query->alias()));



		$tmp   = $query->build_query($new_query, $columns, 'select');
		$new_query = $tmp['query'];
		$objects = $new_query->group_by('group_by_pk')->limit($limit)->offset($offset)->execute($query->connection())->as_array('group_by_pk');

		if (!empty($objects)) {
			$query = $model::find()->where(array($select, 'in', array_keys($objects)));
            $this->applySorting($query, $sorting);

			foreach ($query->get() as $object) {
				$item = array();
				foreach ($this->config['dataset'] as $key => $data) {
                    if (is_array($data)) {
                        $data = $data['value'];
                    }
                    if (is_callable($data)) {
						$item[$key] = $data($object);
					} else {
						$item[$key] = $object->{$data};
					}
				}
				$items[] = $item;
				if ($translatable) {
					$common_id = $object->{$translatable['common_id_property']};
					$keys[] = $common_id;
					$common_ids[$translatable['common_id_property']][] = $common_id;
				}
			}
			if ($translatable) {
				$langs = call_user_func('Cms\Orm_Translatable::orm_notify_class', $model, 'languages', $common_ids);
				foreach ($keys as $key => $common_id) {
					$items[$key]['lang'] = $langs[$common_id];
				}

				foreach ($items as &$item) {
					$flags = '';
					foreach (explode(',', $item['lang']) as $lang) {
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

		$json = array(
			'get' => '',
			'query' =>  '',
			'offset' => $offset,
			'items' => $items,
			'total' => $count,
		);

		if (\Fuel::$env === \Fuel::DEVELOPMENT) {
			$json['get'] = Input::get();
			$json['query'] = (string) $query->get_query();
		}
		if (\Input::get('debug') !== null) {
			\Debug::dump($json);
			exit();
		}

		$response = \Response::forge(\Format::forge()->to_json($json), 200, array(
		     'Content-Type' => 'application/json',
		));
		$response->send(true);
		exit();
	}

}