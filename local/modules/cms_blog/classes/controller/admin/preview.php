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

class Controller_Admin_Preview extends \Cms\Controller_Generic_Admin {

	public function action_index() {

		$body = array(
			'config'  => \Format::forge()->to_json($_POST),
			'preview' => \View::forge('cms_blog::preview')->render(),
		);

		$response = \Response::forge(\Format::forge()->to_json($body), 200, array(
			'Content-Type' => 'application/json',
		));
		$response->send(true);
		exit();
	}
}
