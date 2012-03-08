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

use Cms\Controller;
use Fuel\Core\View;

class Controller_Admin_Popup extends \Cms\Controller_Generic_Admin {

	public function action_index() {
		return View::forge($this->config['views']['index']);
	}
}
