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

use Fuel\Core\Uri;

class Model_User_Group extends Model {
    protected static $_table_name = 'os_group';
    protected static $_primary_key = array('group_id');

    protected static $permissions;
	protected $access;

    public function check_permission($module, $key) {

		if ($key == 'access') {
			$this->load_access($module);
			return $this->access->check($this, $module);
		}

		$args = func_get_args();
		$args = array_slice($args, 2);
		array_unshift($args, $this->group_id);
		$driver = $this->get_permission_driver($module, $key);
		return call_user_func_array(array($driver, 'check'), $args);
    }

	public static function get_permission_driver($module, $key) {

		static::load_permission_driver($module, $key);
		return static::$permissions[$module][$key];
	}

	public function load_access($module) {
		$this->access = Permission::forge('access', '', array(
			'driver' => 'select',
			'title'=> 'Grant access to the module',
			'label' => 'Grant access to the module',
			'driver_config' => array(
				'choices' => array(
					'access' => array(
						'title' => 'Access the module',
					),
				),
			),
		));
	}

    public static function load_permission_driver($module, $key) {

		if (isset(static::$permissions[$module][$key])) {
			return;
		}

		//\Config::load('applications', true);
		//$apps = \Config::get('applications', array());
		\Config::load("$module::permissions", true);
		$permissions = \Config::get("$module::permissions", array());

        static::$permissions[$module][$key] = Permission::forge($module, $key, $permissions[$key]);

		return static::$permissions[$module][$key];
    }
}
