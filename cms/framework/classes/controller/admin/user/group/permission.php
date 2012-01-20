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

use View;

class Controller_Admin_User_Group_Permission extends Controller_Noviusos_Noviusos {

    public function action_edit() {
        if (!empty($_POST)) {
            $this->post_edit();
        }

        if (!empty($_GET['user_id'])) {
            $user = Model_User_User::find($_GET['user_id']);
            $group = reset($user->groups);
        } else {
            $group = Model_User_Group::find($_GET['id']);
        }

        \Config::load(APPPATH.'data'.DS.'config'.DS.'app_installed.php', 'app_installed');
        $apps = \Config::get('app_installed', array());

        $this->template->body = View::forge('admin/user/permission', array(
            'user' => !empty($user) ? $user : null,
            'group' => $group,
            'apps' => $apps,
        ), false);
        
        return $this->template;
    }

    protected function post_edit() {
		
        $group = Model_User_Group::find($_POST['group_id']);
		
		$module = $_POST['module'];
		$access = Model_User_Permission::find('first', array('where' => array(
			array('perm_group_id', $group->group_id),
			array('perm_module', 'access'),
			array('perm_key', $module),
		)));
		
		// Grant of remove access to the module
		if (empty($_POST['access'][$module]) && !empty($access)) {
			$access->delete();
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
		\Response::redirect('/admin/admin/user/group/permission/edit?id='.$group->group_id);
    }
}
