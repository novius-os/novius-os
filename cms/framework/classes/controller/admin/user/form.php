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

class Controller_Admin_User_Form extends \Cms\Controller_Generic_Admin {

    public function action_add() {

        $user = Model_User_User::forge();

        return \View::forge('cms::admin/user/user_add', array(
            'fieldset' => static::fieldset_add($user)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
        ), false);
    }

    public function action_edit($id = false) {
        if ($id === false) {
            $user = null;
        } else {
            $user = Model_User_User::find($id);
        }
        $group = reset($user->groups);


        \Config::load('cms::admin/native_apps', 'natives_apps');
        $natives_apps = \Config::get('natives_apps', array());

        \Config::load(APPPATH.'data'.DS.'config'.DS.'app_installed.php', 'app_installed');
        $apps = \Config::get('app_installed', array());

        $apps = array_merge($natives_apps, $apps);

        return \View::forge('cms::admin/user/user_edit', array(
            'user'   => $user,
            'fieldset' => static::fieldset_edit($user)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
            'permissions' => \View::forge('cms/admin/user/permission', array(
                'user' => $user,
                'group' => $group,
                'apps' => $apps,
            ), false),
        ), false);
    }

    public function action_save_permissions() {

        $group = Model_User_Group::find(\Input::post('group_id'));

		$modules = \Input::post('module');
        foreach ($modules as $module) {
            $access = Model_User_Permission::find('first', array('where' => array(
                array('perm_group_id', $group->group_id),
                array('perm_module', 'access'),
                array('perm_key', $module),
            )));

            // Grant of remove access to the module
            if (empty($_POST['access'][$module]) && !empty($access)) {
                $access->delete();
                \Response::json(array(
                    'notify' => 'Access successfully denied.',
                ));
            }

            if (!empty($_POST['access'][$module]) && empty($access)) {
                $access = new Model_User_Permission();
                $access->perm_group_id   = $group->group_id;
                $access->perm_module     = 'access';
                $access->perm_identifier = '';
                $access->perm_key        = $module;
                $access->save();
            }

            \Config::load('applications', true);
            $apps = \Config::get('applications', array());

            \Config::load("$module::permissions", true);
            $permissions = \Config::get("$module::permissions", array());

            foreach ($permissions as $identifier => $whatever) {
                $driver = $group->get_permission_driver($module, $identifier);
                $driver->save($group, (array) $_POST['permission'][$module][$identifier]);
            }
        }
		\Response::json(array(
            'notify' => 'Permissions successfully saved.',
        ));
    }

    public static function fieldset_add($user) {

        $fields = array(
            'user_id' => array (
                'label' => __('ID: '),
                'widget' => 'text',
            ),
            'user_name' => array (
                'label' => __('Family name'),
                'widget' => '',
                'validation' => array(
                    'required',
                ),
            ),
            'user_firstname' => array (
                'label' => __('First name'),
                'widget' => '',
                'validation' => array(
                    'required',
                ),
            ),
            'user_email' => array(
                'label' => __('Email: '),
                'widget' => '',
                'validation' => array(
                    'required',
                    'valid_email',
                ),
            ),
            'user_last_connection' => array (
                'label' => __('Last login: '),
                'add' => false,
                'widget' => 'date_select',
                'form' => array(
                    'readonly' => true,
                    'date_format' => 'eu_full',
                ),
            ),
            'user_password' => array (
                'label' => __('Password: '),
                'form' => array(
                    'type' => 'password',
                    'value' => '',
                ),
                'validation' => array(
                    'required',
                    'min_length' => array(6),
                ),
            ),
            'password_confirmation' => array (
                'label' => __('Password (confirmation): '),
                'form' => array(
                    'type' => 'password',
                ),
                'validation' => array(
                    'required', // To show the little star
                    'match_field' => array('user_password'),
                ),
            ),
            'save' => array(
                'form' => array(
                    'type' => 'submit',
                    'tag'  => 'button',
                    'class' => 'primary',
                    'value' => __('Save'),
                    'data-icon' => 'check',
                ),
            ),
        );

        $fieldset = \Fieldset::build_from_config($fields, $user, array(
            'success' => function() use ($user) {
                return array(
                    'notify' => 'User successfully created.',
                    'replaceTab' => 'admin/cms/user/form/edit/'.$user->user_id,
                );
            }
        ));

        $fieldset->js_validation();
        return $fieldset;
    }

    public static function fieldset_edit($user) {

        $fields = array(
            'user_id' => array (
                'label' => __('ID: '),
                'widget' => 'text',
            ),
            'user_name' => array (
                'label' => __('Family name'),
                'widget' => '',
                'validation' => array(
                    'required',
                ),
            ),
            'user_firstname' => array (
                'label' => __('First name'),
                'widget' => '',
                'validation' => array(
                    'required',
                ),
            ),
            'user_email' => array(
                'label' => __('Email: '),
                'widget' => '',
                'validation' => array(
                    'required',
                    'valid_email',
                ),
            ),
            'user_last_connection' => array (
                'label' => __('Last login: '),
                'add' => false,
                'widget' => 'date_select',
                'form' => array(
                    'readonly' => true,
                    'date_format' => 'eu_full',
                ),
            ),
            'password_reset' => array (
                'label' => __('Password: '),
                'form' => array(
                    'type' => 'password',
                    'value' => '',
                ),
                'validation' => array(
                    'min_length' => array(6),
                ),
            ),
            'password_confirmation' => array (
                'label' => __('Password (confirmation): '),
                'form' => array(
                    'type' => 'password',
                ),
                'validation' => array(
                    'match_field' => array('password_reset'),
                ),
            ),
            'save' => array(
                'form' => array(
                    'type' => 'submit',
                    'tag'  => 'button',
                    'class' => 'primary',
                    'value' => __('Save'),
                    'data-icon' => 'check',
                ),
            ),
        );

        $fieldset = \Fieldset::build_from_config($fields, $user, array(
            'before_save' => function($user, $data) {
                if (!empty($data['password_reset'])) {
                    $user->user_password = $data['password_reset'];
                    $notify = 'Password successfully changed.';
                }
            },
            'success' => function($user, $data) {
                return array(
                     'notify' => $user->is_changed('user_password') ? 'New password successfully set.' : 'User successfully saved.',
                );
            }
        ));

        $fieldset->js_validation();
        return $fieldset;
    }
}