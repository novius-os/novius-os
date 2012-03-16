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

class Controller_Inspector_Model extends Controller_Extendable {

    protected $config = array(
        'model' => '',
        'limit' => 20,
        'order_by' => null,
    );

    public function action_list()
    {
		if (!\Cms\Auth::check()) {
			\Response::redirect('/admin/cms/login?redirect='.urlencode($_SERVER['REDIRECT_URL']));
			exit();
		}

        $view = View::forge('inspector/model');

        return $view;
    }

    public function action_json()
    {
		if (!\Cms\Auth::check()) {
			\Response::json(403, array(
				'login_page' => \Uri::base(false).'admin/cms/login',
			));
		}

	    $config = $this->config;
	    $where = function($query) use ($config) {
		    Filter::apply($query, $config);

		    return $query;
	    };

	    $return = $this->items(array_merge($this->config['query'], array(
		    'callback' => array($where),
		    'dataset' => $this->config['dataset'],
		    'lang' => Input::get('lang', null),
		    'limit' => intval(Input::get('limit', $this->config['limit'])),
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
}
