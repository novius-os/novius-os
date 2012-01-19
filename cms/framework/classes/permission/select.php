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

class Permission_Select extends Permission_Driver {
	
	protected $choices;
	
	protected $permissions;
	
	protected static $data;
	
	protected function set_options($options = array()) {
		
		$this->choices = $options['choices'];
	}
	
	public function check($group, $key) {
		static::_load_permissions();
		return in_array($key, (array) static::$data[$group->group_id][$this->module]);
	}
	
	public function display($group) {
		echo \View::forge('cms::permission/driver/select', array(
			'group'      => $group,
			'module'     => $this->module,
			'identifier' => $this->identifier,
			'choices'    => $this->choices,
			'driver'     => $this,
		), false);
	}
	
	public function save($group, $data) {
		
		$perms = Model_User_Permission::find('all', array(
            'where' => array(
                array('perm_group_id', $group->group_id),
				array('perm_module', $this->module),
                array('perm_identifier', $this->identifier),
            ),
        ));
		
		// Remove old permissions
        foreach ($perms as $p) {
            $p->delete();
        }
		
		// Add appropriates one
        foreach ($data as $permitted) {
			$p = new Model_User_Permission();
			$p->perm_group_id   = $group->group_id;
			$p->perm_module     = $this->module;
			$p->perm_identifier = $this->identifier;
			$p->perm_key = $permitted;
			$p->save();
        }
	}
	
	protected static function _load_permissions() {
		if (!empty(static::$data)) {
			return;
		}
		
        $group_ids = array();
        foreach (Model_User_Group::find('all') as $g) {
            $group_ids[] = $g->group_id;
        }
        $data = Model_User_Permission::find('all', array(
            'where' => array(
                array('perm_group_id', 'IN', $group_ids),
            ),
        ));

        foreach ($data as $d) {
            static::$data[$d->perm_group_id][$d->perm_module][] = $d->perm_key;
        }
	}
	
}
