<?php

namespace Fuel\Core;

import('phpquickprofiler/console', 'vendor');
import('phpquickprofiler/phpquickprofiler', 'vendor');

use \Console;
use \PhpQuickProfiler;

class Profiler
{
	protected static $profiler = null;

	protected static $query = null;

	public static function init()
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
		if ( ! \Fuel::$is_cli and ! \Input::is_ajax() and ! static::$profiler)
		{
			static::$profiler = new \PhpQuickProfiler(FUEL_START_TIME);
			static::$profiler->queries = array();
			static::$profiler->queryCount = 0;
			static::mark(__METHOD__.' Start');
			\Fuel::$profiling = true;
		}
	}

	public static function mark($label)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($label), true)) !== __AM_CONTINUE__) return $__am_res; 
		static::$profiler and Console::logSpeed($label);
	}

	public static function mark_memory($var = false, $name = 'PHP')
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($var, $name), true)) !== __AM_CONTINUE__) return $__am_res; 
		static::$profiler and Console::logMemory($var, $name);
	}

	public static function console($text)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($text), true)) !== __AM_CONTINUE__) return $__am_res; 
		static::$profiler and Console::log($text);
	}

	public static function output()
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::$profiler ? static::$profiler->display(static::$profiler) : '';
	}

	public static function start($dbname, $sql, $stacktrace = array())
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($dbname, $sql, $stacktrace), true)) !== __AM_CONTINUE__) return $__am_res; 
		if (static::$profiler)
		{
			static::$query = array(
				'sql' => \Security::htmlentities($sql),
				'time' => static::$profiler->getMicroTime(),
				'stacktrace' => $stacktrace,
				'dbname' => $dbname,
			);
			return true;
		}
	}

	public static function stop($text)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($text), true)) !== __AM_CONTINUE__) return $__am_res; 
		if (static::$profiler)
		{
			static::$query['time'] = (static::$profiler->getMicroTime() - static::$query['time']) *1000;
			static::$profiler->queries[] = static::$query;
			static::$profiler->queryCount++;
		}
	}

	public static function delete($text)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($text), true)) !== __AM_CONTINUE__) return $__am_res; 
		static::$query = null;
	}

	public static function app_total()
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
		return array(
			microtime(true) - FUEL_START_TIME,
			memory_get_peak_usage() - FUEL_START_MEM,
		);
	}
}
