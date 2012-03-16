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

class Orm_Behaviour_Tree extends Orm_Behavior
{
	protected $_class = null;
    protected $_parent_relation = null;
    protected $_children_relation = null;

	/**
	 * parent_relation
	 * children_relation
	 */
	protected $_properties = array();

	public function __construct($class)
	{
		$this->_class = $class;
		$this->_properties = call_user_func($class . '::observers', get_class($this));
        $this->_parent_relation = call_user_func($class . '::relations', $this->_properties['parent_relation']);
        $this->_children_relation = call_user_func($class . '::relations', $this->_properties['children_relation']);

        if (false === $this->_parent_relation)
        {
            throw new \Exception('Relation "parent" not found by tree behaviour: '.$this->_class);
        }

        if (false === $this->_children_relation)
        {
            throw new \Exception('Relation "children" not found by tree behaviour: '.$this->_class);
        }
	}

	public function before_search(&$where, &$order_by = array(), &$options = array()) {
		foreach ($where as $k => $w) {
			if ($w[0] == 'parent') {
                $property = $this->_parent_relation->key_from[0];
				if ($w[1] === null) {
					$where[$k] = array($property, 'IS', null);
				} else {
                    $id = $w[1]->id;
                    if (empty($id)) {
                        unset($where[$k]);
                    } else {
                        $where[$k] = array($property, $id);
                    }
				}
			}
		}
	}

    /**
     * Deletes the children recursively
     */
    public function before_delete(\Cms\Orm\Model $object) {
        $this->delete_children($object);
    }

    /**
     * Delete all the children of the item.
     * (will only affect the current language, by design)
     *
     * @param type $object
     */
    public function delete_children($object) {
        foreach ($this->find_children($object) as $child) {
            $child->delete();
        }
    }

    /**
     * Returns all the direct children of the object
     *
     * @param  \Cms\Orm\Model  $object
     * @param  array  $where
     * @param  array  $order_by
     * @param  array  $options
     * @see \Cms\Model_Page_Page::search
     * @return array of \Cms\Model_Page_Page
     */
    public function find_children($object, $where = array(), $order_by = array(), $options = array()) {
        // Search items whose parent is self
        $where[] = array('parent', $object);
        return $object->search($where, $order_by, $options);
    }

    /**
     * Find the parent of the object
     *
     * @return  Orm\Model  The parent object
     */
	public function find_parent($object) {
        return $object->get($this->_properties['parent_relation']);
	}

    /**
     * Sets a new parent for the object
     *
     * @param   Orm\Model The parent object
     * @return  void
     */
	public function set_parent($object, $parent = null) {
        // Check if the object is appropriate
        if (get_class($parent) != $this->_parent_relation->model_to) {
            throw new \Exception(sprintf('Cannot set "parent" to object of type %s in tree behaviour (expected %s): %s',
                    (string) get_class($parent),
                    $this->_parent_relation->model_to,
                    $this->_class
                ));
        }

        $this->set_parent_no_observers($object, $parent);
        $object->observe('before_change_parent');
        if (!$object->is_new()) {
            $object->save();
        }
        $object->observe('after_change_parent');
	}

    /**
     * Get the list of all IDs of the children
     *
     * @param bool $include_self
     * @return array
     */
    public function get_ids_children($object, $include_self = true) {
        $ids = array();
        if ($include_self) {
            $ids[] = $object->get(\Arr::get($object->primary_key(), 0));
        }
        $this->_populate_id_children($object, $this->_properties['children_relation'], $ids);
        return $ids;
    }

    public function find_children_recursive($object, $include_self = true) {

        // This is weird, but it doesn't work when called directly...
        $ids = $this->get_ids_children($object, $include_self);
        if (empty($ids)) {
            return array();
        }
        return $object::search(array(array(\Arr::get($object->primary_key(), 0), 'IN', $this->get_ids_children($object, $include_self))));
    }

    protected static function _populate_id_children($current_item, $children_relation, &$array) {
        $pk = \Arr::get($current_item->primary_key(), 0);
        foreach ($current_item->get($children_relation) as $child) {
            $array[] = $child->get($pk);
            static::_populate_id_children($child, $children_relation, $array);
        }

    }

	public function set_parent_no_observers($object, $parent = null) {
        // Fetch the relation
        $object->get($this->_properties['parent_relation']);
        foreach ($this->_parent_relation->key_from as $i => $k) {
            $object->set($k, $parent->get($this->_parent_relation->key_to[$i]));
        }
	}
}