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

class Controller_User_Form extends Controller_Noviusos_Noviusos {

	public function after($response) {
		
		\Asset::css('http://yui.yahooapis.com/3.3.0/build/cssreset/reset-min.css', array(), 'css');
		
		\Asset::add_path('static/js/jquery/wijmo/Wijmo-Complete');
		\Asset::css('jquery.wijmo-complete.1.5.0.css', array(), 'css');
		
		\Asset::add_path('static/js/jquery/wijmo/Wijmo-Open');
		\Asset::css('jquery.wijmo-open.1.5.0.css', array(), 'css');
		
		\Asset::add_path('static/cms/');
		\Asset::css('laGrid.css', array(), 'css');
		\Asset::css('mystyle.css', array(), 'css');
		
		return parent::after($response);
	}

    public function action_add() {
		
		$body = \View::forge('user/form/add', array(
			'fieldset_add' => static::fieldset_add()->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
		), false);
        
        $this->template->set('body', $body, false);
        
        return $this->template;
    }

    public function action_edit($id) {
		
        $body = \View::forge('user/form/edit', array(
			'user' => Model_User::find($id),
			'fieldset_edit'     => static::fieldset_edit($id)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
			'fieldset_password' => static::fieldset_password($id)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
		), false);
		
		$this->template->set('body', $body, false);
        
        return $this->template;
    }
	
	public static function fieldset_add() {
		
		
        \Config::load('cms::user/form', true);
		$fields = \Config::get('cms::user/form.fields', array());
		
		$form = \Fieldset::build_from_config($fields, '\Cms\Model_User', array(
			'complete' => function($data) {
				$user = new \Cms\Model_User();
				foreach ($data as $name => $value) {
					$user->$name = $value;
				}
				
				try {
					$user->save();
					$body = array(
						'notify' => 'User saved successfully.',
						'redirect' => 'admin/user/form/edit/'.$user->user_id,
						'listener_fire' => array('cms_user.refresh' => true),
					);
				} catch (\Exception $e) {
					$body = array(
						'error' => $e->getMessage(),
					);
				}

				$response = \Response::forge(\Format::forge()->to_json($body), 200, array(
					'Content-Type' => 'application/json',
				));
				$response->send(true);
				exit();
			}
		));
		
		$form->js_validation();
		return $form;
	}
	
	public static function fieldset_edit($id) {
        $user = Model_User::find($id);
		
        \Config::load('cms::user/form', true);
		$fields = \Config::get('cms::user/form.fields', array());
		
		$fieldset_edit = \Fieldset::build_from_config($fields, $user, array(
			'complete' => function($data) use ($user) {
				foreach ($data as $name => $value) {
					$user->$name = $value;
				}
				
				try {
					$user->save();
					$body = array(
						'notify' => 'User saved successfully.',
						'listener_fire' => array('cms_user.refresh' => true),
						'closeTab' => true,
					);
				} catch (\Exception $e) {
					$body = array(
						'error' => $e->getMessage(),
					);
				}

				$response = \Response::forge(\Format::forge()->to_json($body), 200, array(
					'Content-Type' => 'application/json',
				));
				$response->send(true);
				exit();
			},
			'extend' => function($fieldset)  {
				$fieldset->field('user_fullname')->add_rule('min_length', 3);
			},
		));
		$fieldset_edit->js_validation();
		return $fieldset_edit;
	}
	
	public static function fieldset_password($id) {

        $fields = array (
            'user_id' => array (
                'label' => 'ID',
                'widget' => 'text',
            ),
            'user_fullname' => array (
                'label' => 'Full name',
                'widget' => 'text',
            ),
            'old_password' => array (
                'label' => 'Old password',
                'widget' => '',
                'type' => 'password',
                'validation' => array(
                    'required',
                    'min_length' => array(6),
                ),
            ),
            'user_password' => array (
                'label' => 'Password',
                'widget' => 'password',
                'validation' => array(
                    'required',
                    'min_length' => array(6),
                ),
            ),
        );
		
        $user = Model_User::find($id);
		
		$fieldset_password = \Fieldset::build_from_config($fields, $user, array(
			'form_name' => 'edit_user_passwd',
			'extend' => function($fieldset) use ($user) {
				$fieldset->field('old_password')->add_rule(array(
					'check_old_password' => function($value) use ($user) {
						return $user->check_password($value);
					}
				));
			},
			'complete' => function($data) use ($user) {
		
				try {
					foreach ($data as $name => $value) {
						if (substr($name, 0, 5) == 'user_' && $name != 'user_id') {
							$user->$name = $value;
						}
					}
				
					$user->save();
					$body = array(
						'notify' => 'Password changed successfully.',
						'listener_fire' => array('cms_user.refresh' => true),
					);
				} catch (\Exception $e) {
					$body = array(
						'error' => \Fuel::$env == \Fuel::DEVELOPMENT ? $e->getMessage() : 'An error occured.',
					);
				}

				$response = \Response::forge(\Format::forge()->to_json($body), 200, array(
					'Content-Type' => 'application/json',
				));
				$response->send(true);
				exit();
			}
		));
		$fieldset_password->js_validation();
		return $fieldset_password;
	}
}