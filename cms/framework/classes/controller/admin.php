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

class Controller_Admin extends \Fuel\Core\Controller {

	// Admin entry point
	public function action_dispatch() {
        $args = func_get_args();
        end($args);
        $execute = current($args);
        if (is_array($execute)) {
            array_pop($args);
        } else {
            $execute = array();
        }
		$uri = implode('/', $args);
		list($first, $controller, $action) = explode('/', ltrim($uri, '/').'///', 3);

		$first = $first ?: 'noviusos';
		if (!$controller) {
			$controller = $first;
		}
		$action = trim($action, '/');
		if (!$action) {
			$action = 'index';
		}

		$tries = array(
			"$first/admin/$controller/$action",
			"cms/$first/$controller/$action",
			"cms/admin/$first/$controller/$action",
		);

		$request = false;
		foreach ($tries as $try) {
			try {
				$request = \Request::forge($try, false);
				if (!$request->route || !$request->controller || !$request->action) {
					continue;
				}
				break;
			} catch (\Exception $e) {}
		}

		try {
			return $request->execute($execute)->response();
		} catch (\Request404Exception $e) {
			return \Request::forge('cms/404/admin')->execute()->response();
		}
	}
}