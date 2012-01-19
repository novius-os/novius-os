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

use Fuel\Core\File;
use Fuel\Core\View;

class Controller_Tray_Account extends Controller_Noviusos_Noviusos {

    public function action_index() {

		$user = \Session::get('logged_user');
		$fieldset_password = Controller_Admin_User_Form::fieldset_password($user->user_id);
        $this->template->body = View::forge('tray/account', array(
			'logged_user' => $user,
			'fieldset_password' => $fieldset_password,
		), false);
		return $this->template;
	}
	
	public function action_disconnect() {
		\Session::destroy();
		\Response::redirect('/admin/login/reset');
		exit();
	}
}