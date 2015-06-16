<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2015 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Core;

class Cache_Handler_String implements \Cache_Handler_Driver
{
	public function readable($contents)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($contents), false)) !== __AM_CONTINUE__) return $__am_res; 
		return (string) $contents;
	}

	public function writable($contents)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($contents), false)) !== __AM_CONTINUE__) return $__am_res; 
		return (string) $contents;
	}
}
