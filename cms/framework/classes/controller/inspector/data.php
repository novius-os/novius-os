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
use Fuel\Core\View;
use Fuel\Core\Config;


class Controller_Inspector_Data extends Controller_Extendable {

	protected $config = array(
		'data' => '',
	);

	public function action_list()
	{
		$view = View::forge('inspector/plain_data');

		$view->set('data', \Format::forge()->to_json($this->config['data']), false);

		return $view;
	}
}