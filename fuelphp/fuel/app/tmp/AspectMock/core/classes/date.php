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

/**
 * Date Class
 *
 * DateTime replacement that supports internationalization and does correction to GMT
 * when your webserver isn't configured correctly.
 *
 * @package     Fuel
 * @subpackage  Core
 * @category    Core
 * @link        http://docs.fuelphp.com/classes/date.html
 *
 * Notes:
 * - Always returns Date objects, will accept both Date objects and UNIX timestamps
 * - create_time() uses strptime and has currently a very bad hack to use strtotime for windows servers
 * - Uses strftime formatting for dates www.php.net/manual/en/function.strftime.php
 */
class Date
{
	/**
	 * Time constants (and only those that are constant, thus not MONTH/YEAR)
	 */
	const WEEK   = 604800;
	const DAY    = 86400;
	const HOUR   = 3600;
	const MINUTE = 60;

	/**
	 * @var int server's time() offset from gmt in seconds
	 */
	protected static $server_gmt_offset = 0;

	/**
	 * @var string the timezone to be used to output formatted data
	 */
	public static $display_timezone = null;

	public static function _init()
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
		static::$server_gmt_offset	= \Config::get('server_gmt_offset', 0);

		static::$display_timezone = \Config::get('default_timezone') ?: date_default_timezone_get();

		// Ugly temporary windows fix because windows doesn't support strptime()
		// It attempts conversion between glibc style formats and PHP's internal style format (no 100% match!)
		if ( ! function_exists('strptime') && ! function_exists('Fuel\Core\strptime'))
		{
			function strptime($input, $format)
			{
				// convert the format string from glibc to date format (where possible)
				$new_format = str_replace(
					array('%a', '%A', '%d', '%e', '%j', '%u', '%w', '%U', '%V', '%W', '%b', '%B', '%h', '%m', '%C', '%g', '%G', '%y', '%Y', '%H', '%k', '%I', '%l', '%M', '%p', '%P', '%r', '%R', '%S', '%T', '%X', '%z', '%Z', '%c', '%D', '%F', '%s', '%x', '%n', '%t', '%%'),
					array('D', 'l', 'd', 'j', 'N', 'z', 'w', '[^^]', 'W', '[^^]', 'M', 'F', 'M', 'm', '[^^]', 'Y', 'o', 'y', 'Y', 'H', 'G', 'h', 'g', 'i', 'A', 'a', 'H:i:s A', 'H:i', 's', 'H:i:s', '[^^]', 'O', 'T ', '[^^]', 'm/d/Y', 'Y-m-d', 'U', '[^^]', "\n", "\t", '%'),
					$format
				);

				// parse the input
				$parsed = date_parse_from_format($new_format, $input);

				// parse succesful?
				if (is_array($parsed) and empty($parsed['errors']))
				{
					return array(
						'tm_year' => $parsed['year'] - 1900,
						'tm_mon'  => $parsed['month'] - 1,
						'tm_mday' => $parsed['day'],
						'tm_hour' => $parsed['hour'] ?: 0,
						'tm_min'  => $parsed['minute'] ?: 0,
						'tm_sec'  => $parsed['second'] ?: 0,
					);
				}
				else
				{
					$masks = array(
						'%d' => '(?P<d>[0-9]{2})',
						'%m' => '(?P<m>[0-9]{2})',
						'%Y' => '(?P<Y>[0-9]{4})',
						'%H' => '(?P<H>[0-9]{2})',
						'%M' => '(?P<M>[0-9]{2})',
						'%S' => '(?P<S>[0-9]{2})',
					);

					$rexep = "#" . strtr(preg_quote($format), $masks) . "#";

					if ( ! preg_match($rexep, $input, $result))
					{
						return false;
					}

					return array(
						"tm_sec"  => isset($result['S']) ? (int) $result['S'] : 0,
						"tm_min"  => isset($result['M']) ? (int) $result['M'] : 0,
						"tm_hour" => isset($result['H']) ? (int) $result['H'] : 0,
						"tm_mday" => isset($result['d']) ? (int) $result['d'] : 0,
						"tm_mon"  => isset($result['m']) ? ($result['m'] ? $result['m'] - 1 : 0) : 0,
						"tm_year" => isset($result['Y']) ? ($result['Y'] > 1900 ? $result['Y'] - 1900 : 0) : 0,
					);
				}
			}

			// This really is some fugly code, but someone at PHP HQ decided strptime should
			// output this awful array instead of a timestamp LIKE EVERYONE ELSE DOES!!!
		}
	}

	/**
	 * Create Date object from timestamp, timezone is optional
	 *
	 * @param   int     UNIX timestamp from current server
	 * @param   string  valid PHP timezone from www.php.net/timezones
	 * @return  Date
	 */
	public static function forge($timestamp = null, $timezone = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($timestamp, $timezone), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new static($timestamp, $timezone);
	}

	/**
	 * Returns the current time with offset
	 *
	 * @return  Date
	 */
	public static function time($timezone = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($timezone), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::forge(null, $timezone);
	}

	/**
	 * Returns the current time with offset
	 *
	 * @return  string
	 */
	public static function display_timezone($timezone = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($timezone), true)) !== __AM_CONTINUE__) return $__am_res; 
		is_string($timezone) and static::$display_timezone = $timezone;

		return static::$display_timezone;
	}

	/**
	 * Uses the date config file to translate string input to timestamp
	 *
	 * @param   string  date/time input
	 * @param   string  key name of pattern in config file
	 * @return  Date
	 */
	public static function create_from_string($input, $pattern_key = 'local')
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($input, $pattern_key), true)) !== __AM_CONTINUE__) return $__am_res; 
		\Config::load('date', 'date');

		$pattern = \Config::get('date.patterns.'.$pattern_key, null);
		empty($pattern) and $pattern = $pattern_key;

		$time = strptime($input, $pattern);
		if ($time === false)
		{
			throw new \UnexpectedValueException('Input was not recognized by pattern.');
		}

		// convert it into a timestamp
		$timestamp = mktime($time['tm_hour'], $time['tm_min'], $time['tm_sec'],
						$time['tm_mon'] + 1, $time['tm_mday'], $time['tm_year'] + 1900);

		if ($timestamp === false)
		{
			throw new \OutOfBoundsException('Input was invalid.'.(PHP_INT_SIZE == 4 ? ' A 32-bit system only supports dates between 1901 and 2038.' : ''));
		}

		return static::forge($timestamp);
	}

	/**
	 * Fetches an array of Date objects per interval within a range
	 *
	 * @param   int|Date    start of the range
	 * @param   int|Date    end of the range
	 * @param   int|string  Length of the interval in seconds or valid strtotime time difference
	 * @return   array      array of Date objects
	 */
	public static function range_to_array($start, $end, $interval = '+1 Day')
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($start, $end, $interval), true)) !== __AM_CONTINUE__) return $__am_res; 
		$start = ( ! $start instanceof Date) ? static::forge($start) : $start;
		$end   = ( ! $end instanceof Date) ? static::forge($end) : $end;

		is_int($interval) or $interval = strtotime($interval, $start->get_timestamp()) - $start->get_timestamp();

		if ($interval <= 0)
		{
			throw new \UnexpectedValueException('Input was not recognized by pattern.');
		}

		$range   = array();
		$current = $start;

		while ($current->get_timestamp() <= $end->get_timestamp())
		{
			$range[] = $current;
			$current = static::forge($current->get_timestamp() + $interval);
		}

		return $range;
	}

	/**
	 * Returns the number of days in the requested month
	 *
	 * @param   int  month as a number (1-12)
	 * @param   int  the year, leave empty for current
	 * @return  int  the number of days in the month
	 */
	public static function days_in_month($month, $year = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($month, $year), true)) !== __AM_CONTINUE__) return $__am_res; 
		$year  = ! empty($year) ? (int) $year : (int) date('Y');
		$month = (int) $month;

		if ($month < 1 or $month > 12)
		{
			throw new \UnexpectedValueException('Invalid input for month given.');
		}
		elseif ($month == 2)
		{
			if ($year % 400 == 0 or ($year % 4 == 0 and $year % 100 != 0))
			{
				return 29;
			}
		}

		$days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		return $days_in_month[$month-1];
	}

	/**
	 * Returns the time ago
	 *
	 * @param	int		UNIX timestamp from current server
	 * @param	int		UNIX timestamp to compare against. Default to the current time
	 * @param	string	Unit to return the result in
	 * @return	string	Time ago
	 */
	public static function time_ago($timestamp, $from_timestamp = null, $unit = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($timestamp, $from_timestamp, $unit), true)) !== __AM_CONTINUE__) return $__am_res; 
		if ($timestamp === null)
		{
			return '';
		}

		! is_numeric($timestamp) and $timestamp = static::create_from_string($timestamp)->get_timestamp();

		$from_timestamp == null and $from_timestamp = time();

		\Lang::load('date', true);

		$difference = $from_timestamp - $timestamp;
		$periods    = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
		$lengths    = array(60, 60, 24, 7, 4.35, 12, 10);

		for ($j = 0; isset($lengths[$j]) and $difference >= $lengths[$j] and (empty($unit) or $unit != $periods[$j]); $j++)
		{
			$difference /= $lengths[$j];
		}

        $difference = round($difference);

		if ($difference != 1)
		{
			$periods[$j] = \Inflector::pluralize($periods[$j]);
		}

		$text = \Lang::get('date.text', array(
			'time' => \Lang::get('date.'.$periods[$j], array('t' => $difference)),
		));

		return $text;
	}

	/**
	 * @var  int  instance timestamp
	 */
	protected $timestamp;

	/**
	 * @var  string  output timezone
	 */
	protected $timezone;

	public function __construct($timestamp = null, $timezone = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($timestamp, $timezone), false)) !== __AM_CONTINUE__) return $__am_res; 
		is_null($timestamp) and $timestamp = time() + static::$server_gmt_offset;
		! $timezone and $timezone = \Fuel::$timezone;

		$this->timestamp = $timestamp;
		$this->set_timezone($timezone);
	}

	/**
	 * Returns the date formatted according to the current locale
	 *
	 * @param   string	either a named pattern from date config file or a pattern, defaults to 'local'
	 * @param   mixed 	vald timezone, or if true, output the time in local time instead of system time
	 * @return  string
	 */
	public function format($pattern_key = 'local', $timezone = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($pattern_key, $timezone), false)) !== __AM_CONTINUE__) return $__am_res; 
		\Config::load('date', 'date');

		$pattern = \Config::get('date.patterns.'.$pattern_key, $pattern_key);

		// determine the timezone to switch to
		$timezone === true and $timezone = static::$display_timezone;
		is_string($timezone) or $timezone = $this->timezone;

		// Temporarily change timezone when different from default
		if (\Fuel::$timezone != $timezone)
		{
			date_default_timezone_set($timezone);
		}

		// Create output
		$output = strftime($pattern, $this->timestamp);

		// Change timezone back to default if changed previously
		if (\Fuel::$timezone != $timezone)
		{
			date_default_timezone_set(\Fuel::$timezone);
		}

		return $output;
	}

	/**
	 * Returns the internal timestamp
	 *
	 * @return  int
	 */
	public function get_timestamp()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->timestamp;
	}

	/**
	 * Returns the internal timezone
	 *
	 * @return  string
	 */
	public function get_timezone()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->timezone;
	}

	/**
	 * Returns the internal timezone or the display timezone abbreviation
	 *
	 * @return  string
	 */
	public function get_timezone_abbr($display_timezone = false)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($display_timezone), false)) !== __AM_CONTINUE__) return $__am_res; 
		// determine the timezone to switch to
		$display_timezone and $timezone = static::$display_timezone;
		empty($timezone) and $timezone = $this->timezone;

		// Temporarily change timezone when different from default
		if (\Fuel::$timezone != $timezone)
		{
			date_default_timezone_set($timezone);
		}

		// Create output
		$output = date('T');

		// Change timezone back to default if changed previously
		if (\Fuel::$timezone != $timezone)
		{
			date_default_timezone_set(\Fuel::$timezone);
		}

		return $output;
	}

	/**
	 * Change the timezone
	 *
	 * @param   string  timezone from www.php.net/timezones
	 * @return  Date
	 */
	public function set_timezone($timezone)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($timezone), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->timezone = $timezone;

		return $this;
	}

	/**
	 * Allows you to just put the object in a string and get it inserted in the default pattern
	 *
	 * @return  string
	 */
	public function __toString()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->format();
	}
}
