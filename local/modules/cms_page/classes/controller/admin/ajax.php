<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Page;

use Fuel\Core\Config;

class Controller_Admin_Ajax extends \Controller {

	public function before() {
		Config::load('templates', true);
		parent::before();
	}
	
	public function action_wysiwyg($page_id) {
		$id = $_GET['template_id'];
		$data = \Config::get('templates.id-'.$id, null);
		Model_Page::set_wysiwyg(array_keys($data['layout']));
		
		$page = Model_Page::find($page_id);
		foreach ($data['layout'] as $wysiwyg => $coords) {
			$data['content'][$wysiwyg] = $page->wysiwyg($wysiwyg)->wysiwyg_text;
		}
		
		$response = \Response::forge(\Format::forge()->to_json($data), 200, array(
			'Content-Type' => 'application/json',
		));
		$response->send(true);
		exit();
	}
}