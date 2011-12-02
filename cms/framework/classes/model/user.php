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

\Autoloader::add_class('PasswordHash', CMSPATH.'vendor'.DS.'phpass'.DS.'PasswordHash.php');

class Model_User extends Model {
    protected static $_table_name = 'os_user';
    protected static $_primary_key = array('user_id');
	
	protected static $_delete;

    protected static $_many_many = array(
        'groups' => array(
            'key_from' => 'user_id',
            'key_through_from' => 'user_id', // column 1 from the table in between, should match a posts.id
            'table_through' => 'os_user_group', // both models plural without prefix in alphabetical order
            'key_through_to' => 'group_id', // column 2 from the table in between, should match a users.id
            'model_to' => 'Cms\Model_Group',
            'key_to' => 'group_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );
    
    protected static $_observers = array('Orm\\Observer_Self' => array(
		'events' => array('before_save', 'after_save', 'before_delete', 'after_delete'),
	));
    
    public function check_password($password) {
        $ph = new \PasswordHash(8, false);
        return $ph->CheckPassword($password, $this->user_password);
    }
    
    public function _event_before_save() {
		// Don't hash twice
        if ($this->is_changed('user_password')) {
            $ph = new \PasswordHash(8, false);
            $this->user_password = $ph->HashPassword($this->user_password);
        }
    }
    
    public function _event_after_save() {
		// Don't trigger the event in a loop, because we call save() and this will trigger _event_after_save()
		static $already_saved = array();
		if (!empty($already_saved[$this->user_id])) {
			return;
		}
		$already_saved[$this->user_id] = true;
		
		if (empty($this->groups)) {
			$group = new Model_Group();
			$group->group_user_id = $this->user_id;
		} else {
			$group = reset($this->groups);
		}
		$group->group_name = $this->user_fullname;
		$this->groups[] = $group;
		$this->save(array('groups'));
    }
	
	public function _event_before_delete() {
		// Load the groups to delete
		static::$_delete['groups'] = $this->groups;
	}
	public function _event_after_delete() {
		foreach (static::$_delete['groups'] as $group) {
			$group->delete();
		}
	}
    
    public static function hash_password($password) {
        return substr($password, 0, 1).$password.substr($password, -1);
    }

    public function check_permission($app, $key) {
		$args = func_get_args();
        foreach ($this->groups as $g) {
            if (call_user_func_array(array($g, 'check_permission'), $args)) {
                return true;
            }
        }
        return false;
    }

	protected static $_properties = array (
        'user_id' => array (
            'type' => 'int',
            'min' => '0',
            'max' => '4294967295',
            'name' => 'user_id',
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
            'ordinal_position' => 1,
            'display' => '10',
            'comment' => '',
            'extra' => 'auto_increment',
            'key' => 'PRI',
            'privileges' => 'select,insert,update,references',
            
            'label' => 'ID',
            'widget' => array(
                'hide_add'   => true,
                'display_as' => 'text',
            ),
        ),
        'user_fullname' => array (
            'type' => 'string',
            'name' => 'user_fullname',
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
            'ordinal_position' => 2,
            'character_maximum_length' => '100',
            'collation_name' => 'utf8_general_ci',
            'comment' => '',
            'extra' => '',
            'key' => '',
            'privileges' => 'select,insert,update,references',
            
            'label' => 'Full name',
            'widget' => array(
            ),
        ),
        'user_email' => array (
            'type' => 'string',
            'name' => 'user_email',
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
            'ordinal_position' => 3,
            'character_maximum_length' => '100',
            'collation_name' => 'utf8_general_ci',
            'comment' => '',
            'extra' => '',
            'key' => '',
            'privileges' => 'select,insert,update,references',
            
            'label' => 'Email',
            'widget' => array(
            ),
            'validation' => array(
                'valid_email',
            ),
        ),
        'user_password' => array (
            'type' => 'string',
            'name' => 'user_password',
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
            'ordinal_position' => 4,
            'character_maximum_length' => '40',
            'collation_name' => 'utf8_general_ci',
            'comment' => '',
            'extra' => '',
            'key' => '',
            'privileges' => 'select,insert,update,references',
            
            'label' => 'Password',
            'widget' => array(
                'display_as' => 'password',
            ),
            'validation' => array(
                'required',
                'min_length' => array(6),
            ),
        ),
        'user_last_connection' => array (
            'type' => 'string',
            'name' => 'user_last_connection',
            'default' => null,
            'data_type' => 'datetime',
            'null' => false,
            'ordinal_position' => 5,
            'comment' => '',
            'extra' => '',
            'key' => '',
            'privileges' => 'select,insert,update,references',
            
            'label' => 'Last login',
            'widget' => array(
                'hide_add'   => true,
                'display_as' => 'date_select',
            ),
        ),
    );
    
    public static function _init() {
        static::$_properties['user_last_connection']['default'] = \DB::expr('NOW()');
    }
}

