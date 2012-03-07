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
				if ($w[1] == null) {
					$where[$k] = array($property, 'IS', null);
				} else  {
					$where[$k] = array($property, $w[1]->id);
				}
			}
		}
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
        $object->save();
        $object->observe('after_change_parent');
	}

	public function set_parent_no_observers($object, $parent = null) {
        // Fetch the relation
        $object->get($this->_properties['parent_relation']);
        foreach ($this->_parent_relation->key_from as $i => $k) {
            $object->set($k, $parent->get($this->_parent_relation->key_to[$i]));
        }
	}
}