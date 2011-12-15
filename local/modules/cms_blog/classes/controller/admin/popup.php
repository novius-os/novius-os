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

	public function after($response) {
		\Asset::add_path('static/cms/');
		\Asset::css('base.css', array(), 'css');
		\Asset::css('laGrid.css', array(), 'css');
		\Asset::css('mystyle.css', array(), 'css');

		\Asset::add_path('static/cms/js/jquery/wijmo/');
		\Asset::css('aristo/jquery-wijmo.css', array(), 'css');
		\Asset::css('jquery.wijmo-open.1.5.0.css', array(), 'css');
		\Asset::css('jquery.wijmo-complete.1.5.0.css', array(), 'css');

		return parent::after($response);
	}

	public function action_index() {
		$this->template->body = View::forge('cms_blog::popup', array(
			'category_inpsector' => 'admin/cms_blog/config/category/list',
		));
		return $this->template;
	}
}
