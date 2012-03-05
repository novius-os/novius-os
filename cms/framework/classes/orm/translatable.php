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

class Orm_Translatable extends Orm_Behavior
{
	public static function orm_notify_class($model_class, $event, $data)
	{
		if (method_exists(static::instance($model_class), $event))
		{
			return static::instance($model_class)->{$event}($data);
		}
	}

	protected $_class = null;

	/**
	 * lang_property
	 * common_id_property
	 * single_id_property
     * invariant_fields
	 */
	protected $_properties = array();

	public function __construct($class)
	{
		$this->_class = $class;
		$this->_properties = call_user_func($class . '::observers', get_class($this));
	}

	/**
	 * Fill in the lang_common_id and lang properties when creating the object
	 *
	 * @param   Model  The object
	 * @return  void
	 */
	public function before_insert(\Cms\Orm\Model $obj)
	{
		$common_id_property = $this->_properties['common_id_property'];
		$lang_property      = $this->_properties['lang_property'];

        if (empty($obj->$common_id_property)) {
            $obj->$common_id_property = 0;
        }
        if (empty($obj->$lang_property)) {
            // @todo: decide whether we force a lang or we use NULL instead
            $obj->$lang_property = Arr::get($this->_properties['default_lang'], \Config::get('default_lang', 'en_GB'));
        }
	}
    /**
     * Updates the lang_common_id property
     * @param Model $obj
	 * @return  void
     */
	public function after_insert_insert(\Cms\Orm\Model $obj)
	{
		$common_id_property = $this->_properties['common_id_property'];

        if ($obj->$common_id_property == 0) {
            // __get() magic method will retrieve $_primary_key[0]
            $obj->$common_id_property = $this->id;
            $obj->save();
        }
	}

    /**
     * Copies all invariant fields from the main language
     *
     * @param Model $obj
     */
    public function before_save(\Cms\Orm\Model $obj) {
        if (!$obj->is_main_lang()) {
            $obj_main = $obj->find_main_lang();
            foreach ($this->_properties['invariant_fields'] as $invariant) {
                $obj->$invariant = $obj_main->$invariant;
            }
        }
    }

	/**
	 * Returns null if the Model is not translatable. Returns true or false whether the object is in the main language.
	 *
	 * @return  bool
	 */
	public function is_main_lang($object) {
		return $object->{$this->_properties['single_id_property']} !== null;
	}

	/**
	 * Find the object in the main language
	 *
	 * @return  \Cms\Model
	 */
	public function find_main_lang($object) {
		return $object->find_lang('main');
	}

	/**
	 * Find the object in the specified locale. Won't create it when it doesn't exists
	 *
	 * @param string | true $lang Which locale to retrieve.
	 *  - 'main' will return the main language
	 *  - 'all'  will return all the available objects
	 *  - any valid locale
	 */
	public function find_lang($object, $lang = null) {
		$common_id_property = $object->{$this->_properties['common_id_property']};
		if (empty($common_id_property)) {
			// prevents errors
			// false (and not null) because it's an error and should probably not happen
			return false;
		}

		if ($lang == 'all') {
			return $object->find('all', array(
				'where' => array(
					array($this->_properties['common_id_property'], $common_id_property),
				)));
		}

		return $object->find('first', array(
			'where' => array(
				array($this->_properties['common_id_property'], $common_id_property),
				$lang === 'main' ? array($this->_properties['single_id_property'], $common_id_property) : array($this->_properties['lang_property'], $lang),
			)));
	}

	/**
	 * Returns the locale of the current object
	 *
	 * @return string
	 */
	public function get_lang($object) {
		return $object->{$this->_properties['lang_property']};
	}

	/**
	 * Returns all other available locale for this object
	 *
	 * @return array
	 */
	public function get_other_lang($object) {
		$current_lang = $object->get_lang();
		$all = array();
		foreach ($object->find_lang('all') as $object) {
			$lang = $object->{$this->_properties['lang_property']};
			if ($lang != $current_lang) {
				$all[] = $lang;
			}
		}
		return $all;
	}

    /**
     * Returns all available languages for the requested items
     *
     * @param  array  $where
     * @return array  List of available languages for each single_id
     */
	public function languages($where)
	{
		$common_id_property = $this->_properties['common_id_property'];
		$lang_property = $this->_properties['lang_property'];
		$properties = array(
			array($common_id_property, $common_id_property),
			array(\Db::expr('GROUP_CONCAT('.$lang_property.')'), 'list_lang'),
		);

		$query = call_user_func_array('\Db::select', $properties)
				 ->from(call_user_func($this->_class . '::table'))
				 ->group_by($common_id_property);

		foreach ($where as $field_name => $value) {
			if (!empty($value)) {
				if (is_array($value)) {
					$query->where($field_name, 'in', $value);
				} else {
					$query->where($field_name, '=', $value);
				}
			}
		}
		$data = array();
		foreach ($query->execute() as $row) {
			$data[$row[$common_id_property]] = $row['list_lang'];
		}
		return $data;
	}

	public function before_search(&$where, &$order_by = array(), &$options = array()) {
		foreach ($where as $k => $w) {
			if ($w[0] == 'lang_main') {
				if ($w[1] == true) {
					$where[$k] = array($this->_properties['single_id_property'], 'IS NOT', null);
				} else if ($w[1] == false) {
					$where[$k] = array($this->_properties['single_id_property'], 'IS', null);
				}
			}
		}
	}
}