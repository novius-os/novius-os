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

class Controller_User_Permission_Basic extends Controller {

    public function action_edit($group_id, $app) {

        $group = Model_Group::find($group_id);

        \Config::load(APPPATH.'data'.DS.'config'.DS.'app_installed.php', 'app_installed');
        $apps = \Config::get('app_installed', array());
        \Config::load("$app::permissions", true);
        $permissions = \Config::get("$app::permissions", array());

        return View::forge('user/permission/basic', array(
            'group' => $group,
            'app'   => $app,
            'permissions' => $permissions,
        ));
    }

    protected function post_edit() {
        $perms = Model_Permission::find('all', array(
            'where' => array(
                array('perm_group_id', $_POST['group_id']),
            ),
        ));
        foreach ($perms as $p) {
            $p->delete();
        }

        if (empty($_POST['app'])) {
            return;
        }
        foreach ($_POST['app'] as $app => $keys) {
            if (!in_array('access', $keys)) {
                continue;
            }
            foreach ($keys as $key) {
                $p = new Model_Permission();
                $p->perm_group_id = $_POST['group_id'];
                $p->perm_module = $app;
                $p->perm_key = $key;
                $p->save();
            }
        }
    }
}
