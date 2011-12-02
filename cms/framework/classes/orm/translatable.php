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
	 */
	protected $_properties = array();
	
	public function __construct($class)
	{
		$this->_class = $class;
		$this->_properties = call_user_func($class . '::observers', get_class($this));
	}
	
	public function languages($data)
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
		
		foreach ($data as $field_name => $value) {
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