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

class Controller_Admin_User_Form extends Controller_Noviusos_Noviusos {

	public function after($response) {

		\Asset::css('http://yui.yahooapis.com/3.3.0/build/cssreset/reset-min.css', array(), 'css');

		\Asset::add_path('static/js/jquery/wijmo/Wijmo-Complete');
		\Asset::css('jquery.wijmo-complete.1.5.0.css', array(), 'css');

		\Asset::add_path('static/js/jquery/wijmo/Wijmo-Open');
		\Asset::css('jquery.wijmo-open.1.5.0.css', array(), 'css');

		\Asset::add_path('static/cms/');
		\Asset::css('laGrid.css', array(), 'css');
		\Asset::css('form.css', array(), 'css');

		return parent::after($response);
	}

    public function action_add() {

		$body = \View::forge('admin/user/form/add', array(
			'fieldset_add' => static::fieldset_add()->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
		), false);

        $this->template->set('body', $body, false);

        return $this->template;
    }

    public function action_edit($id) {

        $body = \View::forge('admin/user/form/edit', array(
			'user' => Model_User_User::find($id),
			'fieldset_edit'     => static::fieldset_edit($id)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
			'fieldset_password' => static::fieldset_password($id)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
		), false);

		$this->template->set('body', $body, false);

        return $this->template;
    }

	public static function fieldset_add() {


        \Config::load('cms::admin/user/form', true);
		$fields = \Config::get('cms::admin/user/form.fields', array());

		$form = \Fieldset::build_from_config($fields, '\Cms\Model_User', array(
			'complete' => function($data) {
				$user = new \Cms\Model_User_User();
				foreach ($data as $name => $value) {
					if (substr($name, 0, 5) == 'user_') {
						$user->$name = $value;
					}
				}

				try {
					$user->save();
					$body = array(
						'notify' => 'User saved successfully.',
						'redirect' => 'admin/admin/user/form/edit/'.$user->user_id,
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
        $user = Model_User_User::find($id);

        \Config::load('cms::admin/user/form', true);
		$fields = \Config::get('cms::admin/user/form.fields', array());

		$fieldset_edit = \Fieldset::build_from_config($fields, $user, array(
			'form_name' => 'edit_user_infos',
			'success' => function() {
				return array(
					'notify' => 'User saved successfully.',
					'listener_fire' => array('cms_user.refresh' => true),
				);
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
                'form' => array(
                    'type' => 'password',
                ),
                'validation' => array(
                    'required',
                    'min_length' => array(6),
                ),
            ),
            //*
            'new_password' => array (
                'label' => 'New password',
                'form' => array(
                    'type' => 'password',
                ),
                'validation' => array(
                    'required',
                    'min_length' => array(6),
                ),
            ),
            'new_password_confirmation' => array (
                'label' => 'Confirmation',
                'form' => array(
                    'type' => 'password',
                ),
                'validation' => array(
					'match_field' => array('new_password'), // All rules will be satisfied
                ),
            ),
            'save' => array(
                'label' => '',
                'form' => array(
                    'type' => 'submit',
                    'value' => 'Save',
                ),
            ),
        );

        $user = Model_User_User::find($id);

		$fieldset_password = \Fieldset::build_from_config($fields, $user, array(
			'form_name' => 'edit_user_passwd',
			'extend' => function($fieldset) use ($user) {
				$fieldset->field('old_password')->add_rule(array(
					'check_old_password' => function($value) use ($user) {
						return $user->check_password($value);
					}
				));
			},
			'before_save' => function($user, $data) {
				if (!empty($data['new_password'])) {
					$user->user_password = $data['new_password'];
				}
			},
			'success' => function() {
				return array(
					'notify' => 'Password changed successfully.',
					'listener_fire' => array('cms_user.refresh' => true),
				);
			}
		));
		$fieldset_password->js_validation();
		return $fieldset_password;
	}
}