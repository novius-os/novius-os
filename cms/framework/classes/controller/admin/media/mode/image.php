<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


/**
 * @todo delete
 */
namespace Cms;

use Fuel\Core\Config;

class Controller_Admin_Media_Mode_Image extends Controller_Admin_Media_List {

	public function action_index() {
		$this->mp3grid['urljson'] = 'static/cms/js/admin/media/media_image.js';
		return parent::action_index();
	}
}
