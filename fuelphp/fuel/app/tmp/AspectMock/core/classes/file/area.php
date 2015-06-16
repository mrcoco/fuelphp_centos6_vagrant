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

class File_Area
{
	/**
	 * @var	string	path to basedir restriction, null for no restriction
	 */
	protected $basedir = null;

	/**
	 * @var	array	array of allowed extensions, null for all
	 */
	protected $extensions = null;

	/**
	 * @var	string	base url for files, null for not available
	 */
	protected $url = null;

	/**
	 * @var	bool	whether or not to use file locks when doing file operations
	 */
	protected $use_locks = false;

	/**
	 * @var	array	contains file handler per file extension
	 */
	protected $file_handlers = array();

	protected function __construct(array $config = array())
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($config), false)) !== __AM_CONTINUE__) return $__am_res; 
		foreach ($config as $key => $value)
		{
			if (property_exists($this, $key))
			{
				$this->{$key} = $value;
			}
		}

		if ( ! empty($this->basedir))
		{
			$this->basedir = realpath($this->basedir) ?: $this->basedir;
		}
	}

	/**
	 * Factory for area objects
	 *
	 * @param	array
	 * @return	File_Area
	 */
	public static function forge(array $config = array())
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($config), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new static($config);
	}

	/**
	 * Handler factory for given path
	 *
	 * @param	string				path to file or directory
	 * @param	array				optional config
	 * @return	File_Handler_File
	 * @throws	FileAccessException		when outside basedir restriction or disallowed file extension
	 */
	public function get_handler($path, array $config = array(), $content = array())
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $config, $content), false)) !== __AM_CONTINUE__) return $__am_res; 
		$path = $this->get_path($path);

		if (is_file($path))
		{
			$info = pathinfo($path);

			// deal with path names without an extension
			isset($info['extension']) or $info['extension'] = '';

			// check file extension
			if ( ! empty($this->extensions) && ! in_array($info['extension'], $this->extensions))
			{
				throw new \FileAccessException('File operation not allowed: disallowed file extension.');
			}

			// create specific handler when available
			if (array_key_exists($info['extension'], $this->file_handlers))
			{
				$class = '\\'.ltrim($this->file_handlers[$info['extension']], '\\');
				return $class::forge($path, $config, $this);
			}

			return \File_Handler_File::forge($path, $config, $this);
		}
		elseif (is_dir($path))
		{
			return \File_Handler_Directory::forge($path, $config, $this, $content);
		}

		// still here? path is invalid
		throw new \FileAccessException('Invalid path for file or directory.');
	}

	/**
	 * Does this area use file locks?
	 *
	 * @return	bool
	 */
	public function use_locks()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->use_locks;
	}

	/**
	 * Are the shown extensions limited, and if so to which?
	 *
	 * @return	array
	 */
	public function extensions()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->extensions;
	}

	/**
	 * Translate relative path to real path, throws error when operation is not allowed
	 *
	 * @param	string
	 * @return	string
	 * @throws	FileAccessException	when outside basedir restriction or disallowed file extension
	 */
	public function get_path($path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path), false)) !== __AM_CONTINUE__) return $__am_res; 
		$pathinfo = is_dir($path) ? array('dirname' => $path, 'extension' => null, 'basename' => '') : pathinfo($path);

		// make sure we have a dirname to work with
		isset($pathinfo['dirname']) or $pathinfo['dirname'] = '';

		// do we have a basedir, and is the path already prefixed by the basedir? then just deal with the double dots...
		if ( ! empty($this->basedir) && substr($pathinfo['dirname'], 0, strlen($this->basedir)) == $this->basedir)
		{
			$pathinfo['dirname'] = realpath($pathinfo['dirname']);
		}
		else
		{
			// attempt to get the realpath(), otherwise just use path with any double dots taken out when basedir is set (for security)
			$pathinfo['dirname'] = ( ! empty($this->basedir) ? realpath($this->basedir.DS.$pathinfo['dirname']) : realpath($pathinfo['dirname']) )
					?: ( ! empty($this->basedir) ? $this->basedir.DS.str_replace('..', '', $pathinfo['dirname']) : $pathinfo['dirname']);
		}

		// basedir prefix is required when it is set (may cause unexpected errors when realpath doesn't work)
		if ( ! empty($this->basedir) && substr($pathinfo['dirname'], 0, strlen($this->basedir)) != $this->basedir)
		{
			throw new \OutsideAreaException('File operation not allowed: given path is outside the basedir for this area.');
		}

		// check file extension
		if ( ! empty(static::$extensions) && array_key_exists($pathinfo['extension'], static::$extensions))
		{
			throw new \FileAccessException('File operation not allowed: disallowed file extension.');
		}

		return $pathinfo['dirname'].DS.$pathinfo['basename'];
	}

	/**
	 * Translate relative path to accessible path, throws error when operation is not allowed
	 *
	 * @param	string
	 * @return	string
	 * @throws	LogicException	when no url is set or no basedir is set and file is outside DOCROOT
	 */
	public function get_url($path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path), false)) !== __AM_CONTINUE__) return $__am_res; 
		if(empty($this->url))
		{
			throw new \LogicException('File operation now allowed: cannot create a file url without an area url.');
		}

		$path = $this->get_path($path);

		$basedir = $this->basedir;
		empty($basedir) and $basedir = DOCROOT;

		if(stripos($path, $basedir) !== 0)
		{
			throw new \LogicException('File operation not allowed: cannot create file url whithout a basedir and file outside DOCROOT.');
		}

		return rtrim($this->url, '/').'/'.ltrim(str_replace(DS, '/', substr($path, strlen($basedir))), '/');
	}

	/* -------------------------------------------------------------------------------------
	 * Allow all File methods to be used from an area directly
	 * ------------------------------------------------------------------------------------- */

	public function create($basepath, $name, $contents = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($basepath, $name, $contents), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::create($basepath, $name, $contents, $this);
	}

	public function create_dir($basepath, $name, $chmod = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($basepath, $name, $chmod), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::create_dir($basepath, $name, $chmod, $this);
	}

	public function read($path, $as_string = false)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $as_string), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::read($path, $as_string, $this);
	}

	public function read_dir($path, $depth = 0, $filter = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $depth, $filter), false)) !== __AM_CONTINUE__) return $__am_res; 
		$content = \File::read_dir($path, $depth, $filter, $this);
		return $this->get_handler($path, array(), $content);
	}

	public function rename($path, $new_path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $new_path), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::rename($path, $new_path, $this);
	}

	public function rename_dir($path, $new_path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $new_path), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::rename_dir($path, $new_path, $this);
	}

	public function copy($path, $new_path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $new_path), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::copy($path, $new_path, $this);
	}

	public function copy_dir($path, $new_path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $new_path), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::copy_dir($path, $new_path, $this);
	}

	public function delete($path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::delete($path, $this);
	}

	public function delete_dir($path, $recursive = true, $delete_top = true)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $recursive, $delete_top), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::delete_dir($path, $recursive, $delete_top, $this);
	}

	public function update($basepath, $name, $new_content)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($basepath, $name, $new_content), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::update($basepath, $name, $new_content, $this);
	}

	public function get_permissions($path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::get_permissions($path, $this);
	}

	public function get_time($path, $type)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path, $type), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::get_time($path, $type, $this);
	}

	public function get_size($path)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($path), false)) !== __AM_CONTINUE__) return $__am_res; 
		return \File::get_size($path, $this);
	}
}
