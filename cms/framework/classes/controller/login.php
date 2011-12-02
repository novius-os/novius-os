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

class Controller_Login extends Controller_Generic_Admin {
	
    public function action_login() {
		
		(\Input::method() == 'POST') and $error = $this->post_login();
		
		\Asset::add_path('static/cms/');
		\Asset::css('login.css', array(), 'css');
		
        $this->template->body = \View::forge('misc/login', array(
			'error' => $error,
		));
        return $this->template;
    }
	
    public function action_reset() {
		
        $this->template->body = \View::forge('misc/login_reset');
        return $this->template;
    }
	
	public function after($response) {
		
		\Asset::add_path('static/cms/js/jquery/wijmo/');
		\Asset::css('arctic/jquery-wijmo.css', array(), 'css');
		
		return parent::after($response);
	}
	
	protected function post_login() {
		
		$user = Model_User::find('all', array(
			'where' => array(
				'user_email' => $_POST['email'],
			),
		));
		if (empty($user)) {
			return 'Access denied';
		}
		$user = current($user);
		if ($user->check_password($_POST['password'])) {
			\Session::set('logged_user', $user);
			\Response::redirect(urldecode(\Input::get('redirect', '/admin/')));
			exit();
		}
		return 'Access denied';
	}
}