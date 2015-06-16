<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2015 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Orm;

/**
 * Observer base class
 */
abstract class Observer
{
	/**
	 * @var	array	list of created observer instances created
	 */
	protected static $_instances = array();

	/**
	 * Get notified of an event
	 *
	 * @param  Model   $instance
	 * @param  string  $event
	 */
	public static function orm_notify($instance, $event)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($instance, $event), true)) !== __AM_CONTINUE__) return $__am_res; 
		$model_class = get_class($instance);
		if (method_exists(static::instance($model_class), $event))
		{
			static::instance($model_class)->{$event}($instance);
		}
	}

	/**
	 * Create an instance of this observer
	 *
	 * @param  string  name of the model class
	 */
	public static function instance($model_class)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($model_class), true)) !== __AM_CONTINUE__) return $__am_res; 
		$observer = get_called_class();
		if (empty(static::$_instances[$observer][$model_class]))
		{
			static::$_instances[$observer][$model_class] = new static($model_class);
		}

		return static::$_instances[$observer][$model_class];
	}
}
