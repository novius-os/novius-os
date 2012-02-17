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

class Controller_Admin_Inspector_Category extends \Cms\Controller_Inspector_Modeltree {

	public function action_delete($id) {
		$success = false;

		$category = Model_Category::find_by_blgc_id($id);
		if ($category) {
			$category->delete();
			$success = true;
		}

		$response = \Response::json(array(
			'success' => $success,
		));
	}
}