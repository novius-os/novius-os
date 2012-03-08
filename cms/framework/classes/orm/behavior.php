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

abstract class Orm_Behavior extends \Orm\Observer
{

	public static function behavior($instance, $method, $args)
	{
		$model_class = is_object($instance) ? get_class($instance) : $instance;
		if (method_exists(static::instance($model_class), $method))
		{
			if (is_object($instance)) {
				return call_user_func_array(array(static::instance($model_class), $method), array_merge(array($instance), $args));
			} else {
				return call_user_func_array(array(static::instance($model_class), $method), $args);
			}
		}
        throw new Orm\UnknownMethodBehaviorException();
	}

	public static function instance($model_class)
	{
		$behavior = get_called_class();
		if (empty(static::$_instances[$behavior][$model_class]))
		{
			static::$_instances[$behavior][$model_class] = new static($model_class);
		}

		return static::$_instances[$behavior][$model_class];
	}
}

/* End of file observer.php */