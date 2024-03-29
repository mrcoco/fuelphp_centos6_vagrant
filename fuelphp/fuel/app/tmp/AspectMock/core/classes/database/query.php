<?php
/**
 * Database query wrapper.
 *
 * @package    Fuel/Database
 * @category   Query
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */

namespace Fuel\Core;

class Database_Query
{
	/**
	 * @var  int  Query type
	 */
	protected $_type;

	/**
	 * @var  int  Cache lifetime
	 */
	protected $_lifetime;

	/**
	 * @var  string  Cache key
	 */
	protected $_cache_key = null;

	/**
	 * @var  boolean  Cache all results
	 */
	protected $_cache_all = true;

	/**
	 * @var  string  SQL statement
	 */
	protected $_sql;

	/**
	 * @var  array  Quoted query parameters
	 */
	protected $_parameters = array();

	/**
	 * @var  bool  Return results as associative arrays or objects
	 */
	protected $_as_object = false;

	/**
	 * @var  Database_Connection  Connection to use when compiling the SQL
	 */
	protected $_connection = null;

	/**
	 * Creates a new SQL query of the specified type.
	 *
	 * @param string $sql   query string
	 * @param integer $type query type: DB::SELECT, DB::INSERT, etc
	*/
	public function __construct($sql, $type = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($sql, $type), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_type = $type;
		$this->_sql = $sql;
	}

	/**
	 * Return the SQL query string.
	 *
	 * @return  string
	 */
	final public function __toString()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		try
		{
			// Return the SQL string
			return $this->compile();
		}
		catch (\Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * Get the type of the query.
	 *
	 * @return  integer
	 */
	public function type()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->_type;
	}

	/**
	 * Enables the query to be cached for a specified amount of time.
	 *
	 * @param   integer $lifetime  number of seconds to cache or null for default
	 * @param   string  $cache_key name of the cache key to be used or null for default
	 * @param   boolean $cache_all if true, cache all results, even empty ones
	 *
	 * @return  $this
	 */
	public function cached($lifetime = null, $cache_key = null, $cache_all = true)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($lifetime, $cache_key, $cache_all), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_lifetime = $lifetime;
		$this->_cache_all = (bool) $cache_all;
		is_string($cache_key) and $this->_cache_key = $cache_key;

		return $this;
	}

	/**
	 * Returns results as associative arrays
	 *
	 * @return  $this
	 */
	public function as_assoc()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_as_object = false;

		return $this;
	}

	/**
	 * Returns results as objects
	 *
	 * @param   string $class classname or true for stdClass
	 *
	 * @return  $this
	 */
	public function as_object($class = true)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($class), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_as_object = $class;

		return $this;
	}

	/**
	 * Set the value of a parameter in the query.
	 *
	 * @param   string $param parameter key to replace
	 * @param   mixed  $value value to use
	 *
	 * @return  $this
	 */
	public function param($param, $value)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($param, $value), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Add or overload a new parameter
		$this->_parameters[$param] = $value;

		return $this;
	}

	/**
	 * Bind a variable to a parameter in the query.
	 *
	 * @param  string $param parameter key to replace
	 * @param  mixed  $var   variable to use
	 *
	 * @return $this
	 */
	public function bind($param, & $var)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($param, &$var), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Bind a value to a variable
		$this->_parameters[$param] =& $var;

		return $this;
	}

	/**
	 * Add multiple parameters to the query.
	 *
	 * @param array $params list of parameters
	 *
	 * @return  $this
	 */
	public function parameters(array $params)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($params), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Merge the new parameters in
		$this->_parameters = $params + $this->_parameters;

		return $this;
	}

	/**
	 * Set a DB connection to use when compiling the SQL
	 *
	 * @param  mixed  $db
	 *
	 * @return  $this
	 */
	public function set_connection($db)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($db), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ( ! $db instanceof \Database_Connection)
		{
			// Get the database instance
			$db = \Database_Connection::instance($db);
		}
		$this->_connection = $db;

		return $this;
	}

	/**
	 * Compile the SQL query and return it. Replaces any parameters with their
	 * given values.
	 *
	 * @param   mixed $db Database instance or instance name
	 *
	 * @return  string
	 */
	public function compile($db = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($db), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ($this->_connection !== null and $db === null)
		{
			$db = $this->_connection;
		}

		if ( ! $db instanceof \Database_Connection)
		{
			// Get the database instance
			$db = $this->_connection ?: \Database_Connection::instance($db);
		}

		// Import the SQL locally
		$sql = $this->_sql;

		if ( ! empty($this->_parameters))
		{
			// Quote all of the values
			$values = array_map(array($db, 'quote'), $this->_parameters);

			// Replace the values in the SQL
			$sql = \Str::tr($sql, $values);
		}

		return trim($sql);
	}

	/**
	 * Execute the current query on the given database.
	 *
	 * @param   mixed   $db Database instance or name of instance
	 *
	 * @return  object   Database_Result for SELECT queries
	 * @return  mixed    the insert id for INSERT queries
	 * @return  integer  number of affected rows for all other queries
	 */
	public function execute($db = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($db), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ($this->_connection !== null and $db === null)
		{
			$db = $this->_connection;
		}

		if ( ! is_object($db))
		{
			// Get the database instance. If this query is a instance of
			// Database_Query_Builder_Select then use the slave connection if configured
			$db = \Database_Connection::instance($db, null, ! $this instanceof \Database_Query_Builder_Select);
		}

		// Compile the SQL query
		$sql = $this->compile($db);

		// make sure we have a SQL type to work with
		if (is_null($this->_type))
		{
			switch(strtoupper(substr(ltrim($sql, '('), 0, 6)))
			{
				case 'SELECT':
					$this->_type = \DB::SELECT;
					break;
				case 'INSERT':
					$this->_type = \DB::INSERT;
					break;
				case 'UPDATE':
					$this->_type = \DB::UPDATE;
					break;
				case 'DELETE':
					$this->_type = \DB::DELETE;
					break;
				default:
					$this->_type = 0;
			}
		}

		if ($db->caching() and ! empty($this->_lifetime) and $this->_type === \DB::SELECT)
		{
			$cache_key = empty($this->_cache_key) ?
				'db.'.md5('Database_Connection::query("'.$db.'", "'.$sql.'")') : $this->_cache_key;
			$cache = \Cache::forge($cache_key);
			try
			{
				$result = $cache->get();
				return new \Database_Result_Cached($result, $sql, $this->_as_object);
			}
			catch (\CacheNotFoundException $e) {}
		}

		// Execute the query
		\DB::$query_count++;
		$result = $db->query($this->_type, $sql, $this->_as_object);

		// Cache the result if needed
		if (isset($cache) and ($this->_cache_all or $result->count()))
		{
			$cache->set_expiration($this->_lifetime)->set_contents($result->as_array())->set();
		}

		return $result;
	}

}
