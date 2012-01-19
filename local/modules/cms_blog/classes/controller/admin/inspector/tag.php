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

use Fuel\Core\Arr;
use Fuel\Core\Config;

class Controller_Admin_Inspector_Tag extends \Cms\Controller_Inspector_Model {

	public function before($response = null) {
		Config::load('cms_blog::admin/tag', true);
		$this->config = Arr::merge($this->config, Config::get('cms_blog::admin/tag'));

		parent::before($response);
	}
}