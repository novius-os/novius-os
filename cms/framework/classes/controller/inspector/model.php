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

class Controller_Inspector_Model extends \Controller {

    protected $config = array(
        'model' => '',
        'columns' => array(),
    	'input_name'   => '',
        'limit' => 20,
        'urljson' => '',
        'order_by' => null,
    );

    public function action_list()
    {
        $view = View::forge('inspector/model');

        $this->config = ConfigProcessor::process($this->config);

        $view->set('columns', \Format::forge($this->config['columns'])->to_json(), false);
        $view->set('input_name', $this->config['input_name']);
        $view->set('urljson', $this->config['urljson']);
        $view->set('widget_id', $this->config['widget_id']);

        return $view;
    }

    public function action_json()
    {
    	$offset = intval(Input::get('offset', 0));
    	$limit = intval(Input::get('limit', $this->config['limit']));
    	$items = array();

    	$model = $this->config['query']['model'];

    	$query = $model::find();
        Filter::apply($query, $this->config);
    	if ($this->config['query']['related'] && is_array($this->config['query']['related'])) {
	    	foreach ($this->config['query']['related'] as $related) {
	    		$query->related($related);
	    	}
    	}

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
    	$count = $query->count();

    	foreach ($query->rows_limit($limit)->rows_offset($offset)->get() as $object) {
    		$item = array();
    		foreach ($this->config['dataset'] as $key => $data) {
    			if (is_callable($data)) {
    				$item[$key] = $data($object);
    			} else {
    				$item[$key] = $object->{$data};
    			}
    		}
    		$items[] = $item;
    	}

    	$response = \Response::forge(\Format::forge()->to_json(array(
			'get' => \Fuel::$env === \Fuel::DEVELOPMENT ? Input::get() : '',
    		'query' => \Fuel::$env === \Fuel::DEVELOPMENT ? (string) $query->get_query() : '',
    		'offset' => $offset,
    		'items' => $items,
    		'total' => $count,
    	)), 200, array(
    		'Content-Type' => 'application/json',
    	));
    	$response->send(true);
    	exit();
    }
}
