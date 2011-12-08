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

use \Input;
use \Format;
use \View;
use \Config;

class Controller_Inspector_Modeltree extends \Controller {

    protected $config = array(
        'model' => '',
        'columns' => array(),
    	'input_name'   => '',
        'limit' => 20,
        'urljson' => '',
        'order_by' => null,
    );

    public function before() {
        parent::before();

        $model = $this->config['query']['model'];

        $relations = $model::relations();
        $primary = $model::primary_key();
        $this->config['parent_column'] = $relations['parent']->key_from[0];
        $this->config['primary_column'] = $primary[0];
    }

    public function action_list()
    {
        $view = View::forge('inspector/modeltree');

		$view->set('inspector_css', \Format::forge()->to_json(\Arr::merge(array(
			'height' => '100%',
			'width' => '100%',
		), \Arr::get($this->config, 'inspector_css', array()))), false);

		$view->set('wijgrid', \Format::forge()->to_json(\Arr::merge(array(
			'columnsAutogenerationMode' => 'none',
			'scrollMode' => 'auto',
			'allowColSizing' => true,
			'allowColMoving' => true,
			'staticRowIndex' => 0,
			'currentCellChanged' =>  'function(e) {
				var row = $(e.target).wijgrid("currentCell").row(),
					data = row ? row.data : false;

				if (data && rendered) {
					$nos.nos.listener.fire("inspector.selectionChanged." + widget_id, false, ["'.$this->config['input_name'].'", data.id, data.title]);
				}
				inspector.wijgrid("currentCell", -1, -1);
			}',
			'rendering' => 'function() {
				rendered = false;
			}',
			'rendered' => 'function() {
				rendered = true;
				inspector.css("height", "auto");
			}',
		), \Arr::get($this->config, 'wijgrid', array()))), false);

        $view->set('columns', \Format::forge()->to_json($this->config['columns']), false);
        $view->set('input_name', $this->config['input_name']);
        $view->set('urljson', $this->config['urljson']);
        $view->set('widget_id', $this->config['widget_id']);

        return $view;
    }

    public function action_json()
    {
    	$items = $this->items();

        $response = \Response::forge(\Format::forge()->to_json(array(
        	'offset' => 0,
            'items' => $items,
        	'total' => count($items),
        )), 200, array(
            'Content-Type' => 'application/json',
        ));
        $response->send(true);
        exit();

    }

    protected function items($parent_id = null, $level = 0)
    {
        $query = $this->query($parent_id);

        $items = array();
        foreach ($query->get() as $object) {
            $item = array(
                'id' => $object->{$this->config['primary_column']},
                'level' => $level,
            );
            foreach ($this->config['dataset'] as $key => $data) {
            	if (is_callable($data)) {
            		$item[$key] = $data($object);
            	} else {
            		$item[$key] = $object->{$data};
            	}
            }
            $childs = $this->items($object->{$this->config['primary_column']}, $level + 1);
            $item['hasChilds'] = count($childs) ? true : false;
            $items[] = $item;
            $items = array_merge($items, $childs);
        }

        return $items;
    }

    protected function query($parent_id)
    {
        $model = $this->config['query']['model'];

        $query = $model::find();
        $query->where(array($this->config['parent_column'], $parent_id));

        if ($this->config['order_by']) {
            $orders_by = $this->config['order_by'];
            if (!is_array($order_by)) {
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
        return $query;
    }
}