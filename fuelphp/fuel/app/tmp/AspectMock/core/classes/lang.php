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

class LangException extends \FuelException { }

class Lang
{
	/**
	 * @var    array    $loaded_files    array of loaded files
	 */
	public static $loaded_files = array();

	/**
	 * @var  array  language lines
	 */
	public static $lines = array();

	/**
	 * @var  array  language(s) to fall back on when loading a file from the current lang fails
	 */
	public static $fallback;

	public static function _init()
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
		static::$fallback = (array) \Config::get('language_fallback', 'en');
	}

	/**
	 * Returns currently active language.
	 *
	 * @return   string    currently active language
	 */
	public static function get_lang()
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
		$language = \Config::get('language');
		empty($language) and $language = static::$fallback[0];
		return $language;
	}

	/**
	 * Loads a language file.
	 *
	 * @param    mixed        $file        string file | language array | Lang_Interface instance
	 * @param    mixed       $group        null for no group, true for group is filename, false for not storing in the master lang
	 * @param    string|null $language     name of the language to load, null for the configurated language
	 * @param    bool        $overwrite    true for array_merge, false for \Arr::merge
	 * @param    bool        $reload       true to force a reload even if the file is already loaded
	 * @return   array                     the (loaded) language array
	 */
	public static function load($file, $group = null, $language = null, $overwrite = false, $reload = false)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($file, $group, $language, $overwrite, $reload), true)) !== __AM_CONTINUE__) return $__am_res; 
		// get the active language and all fallback languages
		$language or $language = static::get_lang();
		$languages = static::$fallback;

		// make sure we don't have the active language in the fallback array
		if (in_array($language, $languages))
		{
			unset($languages[array_search($language, $languages)]);
		}

		// stick the active language to the front of the list
		array_unshift($languages, $language);

		if ( ! $reload and
		     ! is_array($file) and
		     ! is_object($file) and
		    array_key_exists($language.'/'.$file, static::$loaded_files))
		{
			$group === true and $group = $file;
			if ($group === null or $group === false or ! isset(static::$lines[$language][$group]))
			{
				return false;
			}
			return static::$lines[$language][$group];
		}

		$lang = array();
		if (is_array($file))
		{
			$lang = $file;
		}
		elseif (is_string($file))
		{
			$info = pathinfo($file);
			$type = 'php';
			if (isset($info['extension']))
			{
				$type = $info['extension'];
				// Keep extension when it's an absolute path, because the finder won't add it
				if ($file[0] !== '/' and $file[1] !== ':')
				{
					$file = substr($file, 0, -(strlen($type) + 1));
				}
			}
			$class = '\\Lang_'.ucfirst($type);

			if (class_exists($class))
			{
				static::$loaded_files[$language.'/'.$file] = true;
				$file = new $class($file, $languages);
			}
			else
			{
				throw new \FuelException(sprintf('Invalid lang type "%s".', $type));
			}
		}

		if ($file instanceof Lang_Interface)
		{
			try
			{
				$lang = $file->load($overwrite);
			}
			catch (\LangException $e)
			{
				$lang = array();
			}
			$group = $group === true ? $file->group() : $group;
		}

		isset(static::$lines[$language]) or static::$lines[$language] = array();
		if ($group === null)
		{
			static::$lines[$language] = $overwrite ? array_merge(static::$lines[$language], $lang) : \Arr::merge(static::$lines[$language], $lang);
		}
		else
		{
			$group = ($group === true) ? $file : $group;
			if ($overwrite)
			{
				\Arr::set(static::$lines[$language], $group, array_merge(\Arr::get(static::$lines[$language], $group, array()), $lang));
			}
			else
			{
				\Arr::set(static::$lines[$language], $group, \Arr::merge(\Arr::get(static::$lines[$language], $group, array()), $lang));
			}
		}

		return $lang;
	}

	/**
	 * Save a language array to disk.
	 *
	 * @param   string          $file      desired file name
	 * @param   string|array    $lang      master language array key or language array
	 * @param   string|null     $language  name of the language to load, null for the configurated language
	 * @return  bool                       false when language is empty or invalid else \File::update result
	 */
	public static function save($file, $lang, $language = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($file, $lang, $language), true)) !== __AM_CONTINUE__) return $__am_res; 
		($language === null) and $language = static::get_lang();

		// prefix the file with the language
		if ( ! is_null($language))
		{
			$file = explode('::', $file);
			end($file);
			$file[key($file)] = $language.DS.end($file);
			$file = implode('::', $file);
		}

		if ( ! is_array($lang))
		{
			if ( ! isset(static::$lines[$language][$lang]))
			{
				return false;
			}
			$lang = static::$lines[$language][$lang];
		}

		$type = pathinfo($file, PATHINFO_EXTENSION);
		if( ! $type)
		{
			$type = 'php';
			$file .= '.'.$type;
		}

		$class = '\\Lang_'.ucfirst($type);

		if( ! class_exists($class, true))
		{
			throw new \LangException('Cannot save a language file of type: '.$type);
		}

		$driver = new $class;
		return $driver->save($file, $lang);
	}

	/**
	 * Returns a (dot notated) language string
	 *
	 * @param   string       $line      key for the line
	 * @param   array        $params    array of params to str_replace
	 * @param   mixed        $default   default value to return
	 * @param   string|null  $language  name of the language to get, null for the configurated language
	 * @return  mixed                   either the line or default when not found
	 */
	public static function get($line, array $params = array(), $default = null, $language = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($line, $params, $default, $language), true)) !== __AM_CONTINUE__) return $__am_res; 
		($language === null) and $language = static::get_lang();

		return isset(static::$lines[$language]) ? \Str::tr(\Fuel::value(\Arr::get(static::$lines[$language], $line, $default)), $params) : $default;
	}

	/**
	 * Sets a (dot notated) language string
	 *
	 * @param    string       $line      a (dot notated) language key
	 * @param    mixed        $value     the language string
	 * @param    string       $group     group
	 * @param    string|null  $language  name of the language to set, null for the configurated language
	 * @return   void                    the \Arr::set result
	 */
	public static function set($line, $value, $group = null, $language = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($line, $value, $group, $language), true)) !== __AM_CONTINUE__) return $__am_res; 
		$group === null or $line = $group.'.'.$line;

		($language === null) and $language = static::get_lang();

		isset(static::$lines[$language]) or static::$lines[$language] = array();

		return \Arr::set(static::$lines[$language], $line, \Fuel::value($value));
	}

	/**
	 * Deletes a (dot notated) language string
	 *
	 * @param    string       $item      a (dot notated) language key
	 * @param    string       $group     group
	 * @param    string|null  $language  name of the language to set, null for the configurated language
	 * @return   array|bool              the \Arr::delete result, success boolean or array of success booleans
	 */
	public static function delete($item, $group = null, $language = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($item, $group, $language), true)) !== __AM_CONTINUE__) return $__am_res; 
		$group === null or $item = $group.'.'.$item;

		($language === null) and $language = static::get_lang();

		return isset(static::$lines[$language]) ? \Arr::delete(static::$lines[$language], $item) : false;
	}
}
