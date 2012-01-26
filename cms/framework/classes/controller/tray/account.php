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

class Controller_Tray_Account extends \Controller {

    public function action_index() {


        \Asset::add_path('static/cms/js/vendor/wijmo/');
        \Asset::add_path('static/cms/js/jquery/jquery-ui-noviusos/');
        \Asset::css('aristo/jquery-wijmo.css', array(), 'css');
        \Asset::css('jquery.wijmo-complete.all.2.0.0b2.min.css', array(), 'css');


		$user = \Session::get('logged_user');
		$fieldset_infos    = Controller_Admin_User_Form::fieldset_edit($user->user_id)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');
		$fieldset_password = Controller_Admin_User_Form::fieldset_password($user->user_id)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');
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
		\Response::redirect('/admin/login/reset');
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

                try {

                    $configuration = $user->getConfiguration();
					if (!empty($data['background'])) {
						$media = Model_Media_Media::find($data['background']);
						if (!empty($media)) {
							\Arr::set($configuration, 'misc.display.background', $data['background']);
							$notify = 'Your wallpaper is now "'.$media->media_title.'"';
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

				$body = array();
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
}