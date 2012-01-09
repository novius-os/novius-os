<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Media;

use Fuel\Core\Config;
use Cms\Controller_Mp3table_List;

class Controller_Admin_List extends Controller_Mp3table_List {

	public function before() {
		Config::load('cms_media::admin/media', true);
		$this->config = \Config::getFromUser('cms_media::admin/media', array());

		parent::before();
	}

	public function after($response) {
		\Asset::add_path('static/modules/cms_media/');
		\Asset::css('admin.css', array(), 'css');
		return parent::after($response);
	}
}
