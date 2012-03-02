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

class Orm_Translatable extends \Orm\Observer
{

	protected static $_instances = array();

	public static function orm_notify_class($model_class, $event, $data)
	{
		if (method_exists(static::instance($model_class), $event))
		{
			return static::instance($model_class)->{$event}($data);
		}
	}

	public static function instance($model_class)
	{
		$observer = get_called_class();
		if (empty(static::$_instances[$observer][$model_class]))
		{
			static::$_instances[$observer][$model_class] = new static($model_class);
		}

		return static::$_instances[$observer][$model_class];
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
}