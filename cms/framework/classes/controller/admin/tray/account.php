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

class Controller_Admin_Tray_Account extends \Controller {

    public function action_index() {

        \Asset::add_path('static/cms/js/vendor/wijmo/');
        \Asset::add_path('static/cms/js/jquery/jquery-ui-noviusos/');
        \Asset::css('aristo/jquery-wijmo.css', array(), 'css');
        \Asset::css('jquery.wijmo-complete.all.2.0.3.min.css', array(), 'css');

		$user = \Session::get('logged_user');
		$fieldset_infos    = static::fieldset_edit($user)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');
		$fieldset_password = static::fieldset_password($user)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');
        $fieldset_display  = static::fieldset_display($user)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');

        return View::forge('tray/account', array(
			'logged_user' => $user,
			'fieldset_infos' => $fieldset_infos,
			'fieldset_password' => $fieldset_password,
            'fieldset_display' => $fieldset_display,
		), false);
	}

	public function action_disconnect() {
		\Session::destroy();
		\Response::redirect('/admin/cms/login/reset');
		exit();
	}

    public static function fieldset_display($user) {

		$configuration = $user->getConfiguration();
        $fields = array (
            'background' => array (
                'label' => 'Wallpaper',
                'widget' => 'media',
				'form' => array(
					'value' => \Arr::get($configuration, 'misc.display.background', ''),
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

        $fieldset_display = \Fieldset::build_from_config($fields, $user, array(
            'form_name' => 'edit_user_display',
            'complete' => function($data) use ($user) {

				$body = array();

                try {
                    $configuration = $user->getConfiguration();
					if (!empty($data['background'])) {
						$media = Model_Media_Media::find($data['background']);
						if (!empty($media)) {
							\Arr::set($configuration, 'misc.display.background', $data['background']);
							$notify = 'Your wallpaper is now "'.$media->media_title.'"';
							$body['wallpaper_url'] = \Uri::create($media->get_public_path());
						} else {
							$data['background'] = null;
							$error = 'The selected image does not exists.';
						}
                    }
					if (empty($data['background'])) {
						\Arr::delete($configuration, 'misc.display.background');
						$notify = 'Your wallpaper has been removed.';
					}

                    $user->user_configuration = serialize($configuration);
                    $user->save();
                } catch (\Exception $e) {
                    $error = \Fuel::$env == \Fuel::DEVELOPMENT ? $e->getMessage() : 'An error occured.';
                }

				if (!empty($notify)) {
					$body['notify'] = $notify;
				}
				if (!empty($error)) {
					$body['error'] = $error;
				}

                \Response::json($body);
            }
        ));
        $fieldset_display->js_validation();
        return $fieldset_display;
    }

	public static function fieldset_edit($user) {

        $fields = array(
            'user_name' => array (
                'label' => 'Family name',
                'widget' => '',
                'validation' => array(
                    'required',
                ),
            ),
            'user_firstname' => array (
                'label' => 'First name',
                'widget' => '',
                'validation' => array(
                    'required',
                ),
            ),
            'user_email' => array(
                'label' => 'Email',
                'widget' => '',
                'validation' => array(
                    'required',
                    'valid_email',
                ),
            ),
            'user_last_connection' => array (
                'label' => 'Last login',
                'add' => false,
                'widget' => 'date_select',
                'form' => array(
                    'readonly' => true,
                    'date_format' => 'eu_full',
                ),
            ),
            'save' => array(
                'label' => '',
                'form' => array(
                    'type' => 'submit',
                    'tag' => 'button',
                    'data-icon' => 'check',
                    'value' => __('Save'),
                ),
            )
        );

		$fieldset_edit = \Fieldset::build_from_config($fields, $user, array(
			'form_name' => 'edit_user_infos',
			'success' => function() {
				return array(
					'notify' => 'User saved successfully.',
					'fireEvent' => array(
						'event' => 'reload',
						'target' => 'cms_user_user',
					),
				);
			},
			'extend' => function($fieldset)  {
				$fieldset->field('user_name')->add_rule('min_length', 3);
				$fieldset->field('user_firstname')->add_rule('min_length', 3);
			},
		));
		$fieldset_edit->js_validation();
		return $fieldset_edit;
	}

	public static function fieldset_password($user) {

        $fields = array (
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
					'fireEvent' => array(
						'event' => 'reload',
						'target' => 'cms_user_user',
					),
				);
			}
		));
		$fieldset_password->js_validation();
		return $fieldset_password;
	}
}