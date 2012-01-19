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

class Auth {

	public static function login($login, $password) {

		$user = Model_User_User::find('all', array(
			'where' => array(
				'user_email' => $login,
			),
		));
		if (empty($user)) {
			return false;
		}
		$user = current($user);
		if ($user->check_password($password)) {
			\Session::set('logged_user', $user);
			return true;
		}
		return false;
	}

	public static function check() {

        // Might be great to add some additional verifications here !
		$logged_user = \Session::get('logged_user', false);
		if (empty($logged_user)) {
			return false;
		} else {
            $logged_user = Model_User_User::find_by_user_id($logged_user->id); // We reload the user
            \Session::set('logged_user', $logged_user);
			return true;
        }
	}
}
