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

class Controller_Admin_User_User extends Controller_Extendable {

    protected static function  _get_user_with_permission($user_id, $permission) {
        if (empty($user_id)) {
            throw new \Exception('No user specified.');
        }
        $media = Model_User_User::find($user_id);
        if (empty($media)) {
            throw new \Exception('User not found.');
        }
        if (!static::check_permission_action('delete', 'controller/admin/media/mp3grid/list', $media)) {
            throw new \Exception('Permission denied');
        }
        return $media;
    }

	public function action_delete_user($user_id = null) {
        try {
            $user = static::_get_user_with_permission($user_id, 'delete');
            return \View::forge('cms::admin/user/user_delete', array(
                'user'       => $user,
            ));
        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
            \Response::json($body);
		}
    }

	public function action_delete_user_confirm() {
        try {
            $user_id = \Input::post('id');
            // Allow GET for easier dev
            if (empty($user_id) && \Fuel::$env == \Fuel::DEVELOPMENT) {
                $user_id = \Input::get('id');
            }

            $user = static::_get_user_with_permission($user_id, 'delete');

            // Delete database & relations (link)
            $user->delete();

			$body = array(
				'notify' => 'User permanently deleted.',
                'fireEvent' => array(
                    'event' => 'reload',
	                'target' => 'cms_user_user',
                ),

			);
        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
    }
}