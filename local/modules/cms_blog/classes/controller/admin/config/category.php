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

class Controller_Admin_Config_Category extends \Cms\Controller_Inspector_Modeltree {

	public function before() {
		Config::load('cms_blog::admin/category', true);
		$this->config = Arr::merge($this->config, Config::get('cms_blog::admin/category'));

		$this->config['inspector_css'] = array('width' => '100%');
		$this->config['wijgrid'] = array(
			'staticRowIndex' => -1,
			'scrollMode' => 'none',
			'highlightCurrentCell' => true,
			'selectionMode' => 'multiRow',
			'currentCellChanged' => 'function(e) {
				var row = $(e.target).wijgrid("currentCell").row(),
					data = row ? row.data : false;

				inspector.wijgrid("currentCell", -1, -1);
			}',
		);
		$this->config['columns'] = array($this->config['columns'][0]);

		parent::before();
	}
}