<?php
/**
 * Database connection wrapper. All database object instances are referenced
 * by a name. Queries are typically handled by [Database_Query], rather than
 * using the database object directly.
 *
 * @package    Fuel/Database
 * @category   Base
 * @author     Kohana Team
 * @author     Nested transactions - Sergey Ogarkov, sogarkov@gmail.com
 * @copyright  (c) 2008-2010 Kohana Team
 * @license    http://kohanaphp.com/license
 */

namespace Fuel\Core;

abstract class Database_Connection
{
	/**
	 * @var string Cache of the name of the readonly connection
	 */
	protected static $_readonly = array();

	/**
	 * @var  array  Database instances
	 */
	public static $instances = array();

	/**
	 * Get a singleton Database instance. If configuration is not specified,
	 * it will be loaded from the database configuration file using the same
	 * group as the name.
	 *
	 *     // Load the default database
	 *     $db = static::instance();
	 *
	 *     // Create a custom configured instance
	 *     $db = static::instance('custom', $config);
	 *
	 * @param   string $name     instance name
	 * @param   array  $config   configuration parameters
	 * @param   bool   $writable when replication is enabled, whether to return the master connection
	 *
	 * @return  Database_Connection
	 *
	 * @throws \FuelException
	 */
	public static function instance($name = null, array $config = null, $writable = true)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($name, $config, $writable), true)) !== __AM_CONTINUE__) return $__am_res; 
		\Config::load('db', true);
		if ($name === null)
		{
			// Use the default instance name
			$name = \Config::get('db.active');
		}

		if ( ! $writable and ($readonly = \Config::get('db.'.$name.'.readonly', false)))
		{
			! isset(static::$_readonly[$name]) and static::$_readonly[$name] = \Arr::get($readonly, array_rand($readonly));
			$name = static::$_readonly[$name];
		}

		if ( ! isset(static::$instances[$name]))
		{
			if ($config === null)
			{
				// Load the configuration for this database
				$config = \Config::get('db.'.$name);
			}

			if ( ! isset($config['type']))
			{
				throw new \FuelException('Database type not defined in "'.$name.'" configuration or "'.$name.'" configuration does not exist');
			}

			// Set the driver class name
			$driver = '\\Database_' . ucfirst($config['type']) . '_Connection';

			// Create the database connection instance
			new $driver($name, $config);
		}

		return static::$instances[$name];
	}

	/**
	 * @var  string  the last query executed
	 */
	public $last_query;

	/**
	 * @var  string  Character that is used to quote identifiers
	 */
	protected $_identifier = '"';

	/**
	 * @var  string  Instance name
	 */
	protected $_instance;

	/**
	 *
	 * @var bool $_in_transation allows transactions
	 */
	protected $_in_transaction = false;

	/**
	 *
	 * @var int Transaction nesting depth counter.
	 * Should be modified AFTER a driver has changed the level successfully
	 */
	protected $_transaction_depth = 0;

	/**
	 * @var  resource  Raw server connection
	 */
	protected $_connection;

	/**
	 * @var  array  Configuration array
	 */
	protected $_config;

	/**
	 * Stores the database configuration locally and name the instance.
	 *
	 * [!!] This method cannot be accessed directly, you must use [static::instance].
	 *
	 * @param string $name
	 * @param array  $config
	 */
	protected function __construct($name, array $config)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($name, $config), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Set the instance name
		$this->_instance = $name;

		// Store the config locally
		$this->_config = $config;

		// Store the database instance
		static::$instances[$name] = $this;
	}

	/**
	 * Disconnect from the database when the object is destroyed.
	 *
	 *     // Destroy the database instance
	 *     unset(static::instances[(string) $db], $db);
	 *
	 * [!!] Calling `unset($db)` is not enough to destroy the database, as it
	 * will still be stored in `static::$instances`.
	 *
	 * @return  void
	 */
	final public function __destruct()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->disconnect();
	}

	/**
	 * Returns the database instance name.
	 *
	 *     echo (string) $db;
	 *
	 * @return  string
	 */
	final public function __toString()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->_instance;
	}

	/**
	 * Connect to the database. This is called automatically when the first
	 * query is executed.
	 *
	 *     $db->connect();
	 *
	 * @throws  Database_Exception
	 * @return  void
	 */
	abstract public function connect();

	/**
	 * Disconnect from the database. This is called automatically by [static::__destruct].
	 *
	 *     $db->disconnect();
	 *
	 * @return  boolean
	 */
	abstract public function disconnect();

	/**
	 * Set the connection character set. This is called automatically by [static::connect].
	 *
	 *     $db->set_charset('utf8');
	 *
	 * @throws  Database_Exception
	 * @param   string $charset character set name
	 * @return  void
	 */
	abstract public function set_charset($charset);

	/**
	 * Perform an SQL query of the given type.
	 *
	 *     // Make a SELECT query and use objects for results
	 *     $db->query(static::SELECT, 'SELECT * FROM groups', true);
	 *
	 *     // Make a SELECT query and use "Model_User" for the results
	 *     $db->query(static::SELECT, 'SELECT * FROM users LIMIT 1', 'Model_User');
	 *
	 * @param   integer $type      static::SELECT, static::INSERT, etc
	 * @param   string  $sql       SQL query
	 * @param   mixed   $as_object result object class, true for stdClass, false for assoc array
	 *
	 * @return  object   Database_Result for SELECT queries
	 * @return  array    list (insert id, row count) for INSERT queries
	 * @return  integer  number of affected rows for all other queries
	 */
	abstract public function query($type, $sql, $as_object);

	/**
	 * Count the number of records in the last query, without LIMIT or OFFSET applied.
	 *
	 *     // Get the total number of records that match the last query
	 *     $count = $db->count_last_query();
	 *
	 * @return  integer
	 */
	public function count_last_query()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ($sql = $this->last_query)
		{
			$sql = trim($sql);
			if (stripos($sql, 'SELECT') !== 0)
			{
				return false;
			}

			if (stripos($sql, 'LIMIT') !== false)
			{
				// Remove LIMIT from the SQL
				$sql = preg_replace('/\sLIMIT\s+[^a-z\)]+/i', ' ', $sql);
			}

			if (stripos($sql, 'OFFSET') !== false)
			{
				// Remove OFFSET from the SQL
				$sql = preg_replace('/\sOFFSET\s+\d+/i', '', $sql);
			}

			// Get the total rows from the last query executed
			$result = $this->query(
				\DB::SELECT,
				'SELECT COUNT(*) AS '.$this->quote_identifier('total_rows').' '.
				'FROM ('.$sql.') AS '.$this->quote_table('counted_results'),
				true
			);

			// Return the total number of rows from the query
			return (int) $result->current()->total_rows;
		}

		return false;
	}

	/**
	 * Per connection cache controlle setter/getter
	 *
	 * @param   bool   $bool  whether to enable it [optional]
	 *
	 * @return  mixed  cache boolean when getting, current instance when setting.
	 */
	public function caching($bool = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($bool), false)) !== __AM_CONTINUE__) return $__am_res; 
		if (is_bool($bool))
		{
			$this->_config['enable_cache'] = $bool;
			return $this;
		}
		return \Arr::get($this->_config, 'enable_cache', true);
	}

	/**
	 * Count the number of records in a table.
	 *
	 *     // Get the total number of records in the "users" table
	 *     $count = $db->count_records('users');
	 *
	 * @param   mixed $table table name string or array(query, alias)
	 *
	 * @return  integer
	 */
	public function count_records($table)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($table), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Quote the table name
		$table = $this->quote_table($table);

		return $this->query(\DB::SELECT, 'SELECT COUNT(*) AS total_row_count FROM '.$table, false)
			->get('total_row_count');
	}

	/**
	 * Returns a normalized array describing the SQL data type
	 *
	 *     $db->datatype('char');
	 *
	 * @param   string $type SQL data type
	 *
	 * @return  array
	 */
	public function datatype($type)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($type), false)) !== __AM_CONTINUE__) return $__am_res; 
		static $types = array(
			// SQL-92
			'bit'                           => array('type' => 'string', 'exact' => true),
			'bit varying'                   => array('type' => 'string'),
			'char'                          => array('type' => 'string', 'exact' => true),
			'char varying'                  => array('type' => 'string'),
			'character'                     => array('type' => 'string', 'exact' => true),
			'character varying'             => array('type' => 'string'),
			'date'                          => array('type' => 'string'),
			'dec'                           => array('type' => 'float', 'exact' => true),
			'decimal'                       => array('type' => 'float', 'exact' => true),
			'double precision'              => array('type' => 'float'),
			'float'                         => array('type' => 'float'),
			'int'                           => array('type' => 'int', 'min' => '-2147483648', 'max' => '2147483647'),
			'integer'                       => array('type' => 'int', 'min' => '-2147483648', 'max' => '2147483647'),
			'interval'                      => array('type' => 'string'),
			'national char'                 => array('type' => 'string', 'exact' => true),
			'national char varying'         => array('type' => 'string'),
			'national character'            => array('type' => 'string', 'exact' => true),
			'national character varying'    => array('type' => 'string'),
			'nchar'                         => array('type' => 'string', 'exact' => true),
			'nchar varying'                 => array('type' => 'string'),
			'numeric'                       => array('type' => 'float', 'exact' => true),
			'real'                          => array('type' => 'float'),
			'smallint'                      => array('type' => 'int', 'min' => '-32768', 'max' => '32767'),
			'time'                          => array('type' => 'string'),
			'time with time zone'           => array('type' => 'string'),
			'timestamp'                     => array('type' => 'string'),
			'timestamp with time zone'      => array('type' => 'string'),
			'varchar'                       => array('type' => 'string'),

			// SQL:1999
			'binary large object'               => array('type' => 'string', 'binary' => true),
			'blob'                              => array('type' => 'string', 'binary' => true),
			'boolean'                           => array('type' => 'bool'),
			'char large object'                 => array('type' => 'string'),
			'character large object'            => array('type' => 'string'),
			'clob'                              => array('type' => 'string'),
			'national character large object'   => array('type' => 'string'),
			'nchar large object'                => array('type' => 'string'),
			'nclob'                             => array('type' => 'string'),
			'time without time zone'            => array('type' => 'string'),
			'timestamp without time zone'       => array('type' => 'string'),

			// SQL:2003
			'bigint'    => array('type' => 'int', 'min' => '-9223372036854775808', 'max' => '9223372036854775807'),

			// SQL:2008
			'binary'            => array('type' => 'string', 'binary' => true, 'exact' => true),
			'binary varying'    => array('type' => 'string', 'binary' => true),
			'varbinary'         => array('type' => 'string', 'binary' => true),
		);

		if (isset($types[$type]))
		{
			return $types[$type];
		}

		return array();
	}

	/**
	 * List all of the tables in the database. Optionally, a LIKE string can
	 * be used to search for specific tables.
	 *
	 *     // Get all tables in the current database
	 *     $tables = $db->list_tables();
	 *
	 *     // Get all user-related tables
	 *     $tables = $db->list_tables('user%');
	 *
	 * @param   string $like table to search for
	 *
	 * @return  array
	 */
	abstract public function list_tables($like = null);

	/**
	 * Lists all of the columns in a table. Optionally, a LIKE string can be
	 * used to search for specific fields.
	 *
	 *     // Get all columns from the "users" table
	 *     $columns = $db->list_columns('users');
	 *
	 *     // Get all name-related columns
	 *     $columns = $db->list_columns('users', '%name%');
	 *
	 * @param   string $table table to get columns from
	 * @param   string $like  column to search for
	 *
	 * @return  array
	 */
	abstract public function list_columns($table, $like = null);

	/**
	 * Extracts the text between parentheses, if any.
	 *
	 *     // Returns: array('CHAR', '6')
	 *     list($type, $length) = $db->_parse_type('CHAR(6)');
	 *
	 * @param string $type
	 *
	 * @return  array   list containing the type and length, if any
	 */
	protected function _parse_type($type)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($type), false)) !== __AM_CONTINUE__) return $__am_res; 
		if (($open = strpos($type, '(')) === false)
		{
			// No length specified
			return array($type, null);
		}

		// Closing parenthesis
		$close = strpos($type, ')', $open);

		// Length without parentheses
		$length = substr($type, $open + 1, $close - 1 - $open);

		// Type without the length
		$type = substr($type, 0, $open).substr($type, $close + 1);

		return array($type, $length);
	}

	/**
	 * Return the table prefix defined in the current configuration.
	 *
	 *     $prefix = $db->table_prefix();
	 *
	 * @param string $table
	 *
	 * @return  string
	 */
	public function table_prefix($table = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($table), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ($table !== null)
		{
			return $this->_config['table_prefix'] .$table;
		}

		return $this->_config['table_prefix'];
	}

	/**
	 * Quote a value for an SQL query.
	 *
	 *     $db->quote(null);   // 'null'
	 *     $db->quote(10);     // 10
	 *     $db->quote('fred'); // 'fred'
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Database_Expression] objects will use the value of the expression.
	 * [Database_Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param   mixed $value any value to quote
	 *
	 * @return  string
	 *
	 * @uses    static::escape
	 */
	public function quote($value)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($value), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ($value === null)
		{
			return 'null';
		}
		elseif ($value === true)
		{
			return "'1'";
		}
		elseif ($value === false)
		{
			return "'0'";
		}
		elseif (is_object($value))
		{
			if ($value instanceof Database_Query)
			{
				// Create a sub-query
				return '('.$value->compile($this).')';
			}
			elseif ($value instanceof Database_Expression)
			{
				// Use a raw expression
				return $value->value();
			}
			else
			{
				// Convert the object to a string
				return $this->quote((string) $value);
			}
		}
		elseif (is_array($value))
		{
			return '('.implode(', ', array_map(array($this, __FUNCTION__), $value)).')';
		}
		elseif (is_int($value))
		{
			return (int) $value;
		}
		elseif (is_float($value))
		{
			// Convert to non-locale aware float to prevent possible commas
			return sprintf('%F', $value);
		}

		return $this->escape($value);
	}

	/**
	 * Quote a database table name and adds the table prefix if needed.
	 *
	 *     $table = $db->quote_table($table);
	 *
	 * @param   mixed $value table name or array(table, alias)
	 *
	 * @return  string
	 *
	 * @uses    static::quote_identifier
	 * @uses    static::table_prefix
	 */
	public function quote_table($value)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($value), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Assign the table by reference from the value
		if (is_array($value))
		{
			$table =& $value[0];

			// Attach table prefix to alias
			$value[1] = $this->table_prefix().$value[1];
		}
		else
		{
			$table =& $value;
		}

		// deal with the sub-query objects first
		if ($table instanceof Database_Query)
		{
			// Create a sub-query
			$table = '('.$table->compile($this).')';
		}
		elseif (is_string($table))
		{
			if (strpos($table, '.') === false)
			{
				// Add the table prefix for tables
				$table = $this->quote_identifier($this->table_prefix().$table);
			}
			else
			{
				// Split the identifier into the individual parts
				$parts = explode('.', $table);

				if ($prefix = $this->table_prefix())
				{
					// Get the offset of the table name, 2nd-to-last part
					// This works for databases that can have 3 identifiers (Postgre)
					if (($offset = count($parts)) == 2)
					{
						$offset = 1;
					}
					else
					{
						$offset = $offset - 2;
					}

					// Add the table prefix to the table name
					$parts[$offset] = $prefix.$parts[$offset];
				}

				// Quote each of the parts
				$table = implode('.', array_map(array($this, 'quote_identifier'), $parts));
			}
		}

		// process the alias if present
		if (is_array($value))
		{
			// Separate the column and alias
			list($value, $alias) = $value;

			return $value.' AS '.$this->quote_identifier($alias);
		}
		else
		{
			// return the value
			return $value;
		}
	}

	/**
	 * Quote a database identifier, such as a column name. Adds the
	 * table prefix to the identifier if a table name is present.
	 *
	 *     $column = $db->quote_identifier($column);
	 *
	 * You can also use SQL methods within identifiers.
	 *
	 *     // The value of "column" will be quoted
	 *     $column = $db->quote_identifier('COUNT("column")');
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Database_Expression] objects will use the value of the expression.
	 * [Database_Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param   mixed $value any identifier
	 *
	 * @return  string
	 *
	 * @uses    static::table_prefix
	 */
	public function quote_identifier($value)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($value), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ($value === '*')
		{
			return $value;
		}
		elseif (is_object($value))
		{
			if ($value instanceof Database_Query)
			{
				// Create a sub-query
				return '('.$value->compile($this).')';
			}
			elseif ($value instanceof Database_Expression)
			{
				// Use a raw expression
				return $value->value();
			}
			else
			{
				// Convert the object to a string
				return $this->quote_identifier((string) $value);
			}
		}
		elseif (is_array($value))
		{
			// Separate the column and alias
			list($value, $alias) = $value;

			return $this->quote_identifier($value).' AS '.$this->quote_identifier($alias);
		}

		if (preg_match('/^(["\']).*\1$/m', $value))
		{
			return $value;
		}

		if (strpos($value, '.') !== false)
		{
			// Split the identifier into the individual parts
			// This is slightly broken, because a table or column name
			// (or user-defined alias!) might legitimately contain a period.
			$parts = explode('.', $value);

			if ($prefix = $this->table_prefix())
			{
				// Get the offset of the table name, 2nd-to-last part
				// This works for databases that can have 3 identifiers (Postgre)
				$offset = count($parts) - 2;

				// Add the table prefix to the table name
				$parts[$offset] = $prefix.$parts[$offset];
			}

			// Quote each of the parts
			return implode('.', array_map(array($this, __FUNCTION__), $parts));
		}

		// That you can simply escape the identifier by doubling
		// it is a built-in assumption which may not be valid for
		// all connection types!  However, it's true for MySQL,
		// SQLite, Postgres and other ANSI SQL-compliant DBs.
		return $this->_identifier.str_replace($this->_identifier, $this->_identifier.$this->_identifier, $value).$this->_identifier;
	}

	/**
	 * Sanitize a string by escaping characters that could cause an SQL
	 * injection attack.
	 *
	 *     $value = $db->escape('any string');
	 *
	 * @param   string $value value to quote
	 *
	 * @return  string
	 */
	abstract public function escape($value);

	/**
	 * Whether or not the connection is in transaction mode
	 *
	 *     $db->in_transaction();
	 *
	 * @return bool
	 */
	public function in_transaction()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->_in_transaction;
	}

	/**
	 * Begins a nested transaction on instance
	 *
	 *     $db->start_transaction();
	 *
	 * @return bool
	 */
	public function start_transaction()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$result = true;

		if ($this->_transaction_depth == 0)
		{
			if ($this->driver_start_transaction())
			{
				$this->_in_transaction = true;
			}
			else
			{
				$result = false;
			}
		}
		else
		{
			$result = $this->set_savepoint($this->_transaction_depth);
			// If savepoint is not supported it is not an error
			isset($result) or $result = true;
		}

		$result and $this->_transaction_depth ++;

		return $result;
	}

	/**
	 * Commits nested transaction
	 *
	 *     $db->commit_transaction();
	 *
	 * @return bool
	 */
	public function commit_transaction()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Fake call of the commit
		if ($this->_transaction_depth <= 0)
		{
			return false;
		}

		if ($this->_transaction_depth - 1)
		{
			$result = $this->release_savepoint($this->_transaction_depth - 1);
			// If savepoint is not supported it is not an error
			! isset($result) and $result = true;
		}
		else
		{
			$this->_in_transaction = false;
			$result = $this->driver_commit();
		}

		$result and $this->_transaction_depth --;

		return $result;
	}

	/**
	 * Rollsback nested pending transaction queries.
	 * Rollback to the current level uses SAVEPOINT,
	 * it does not work if current RDBMS does not support them.
	 * In this case system rollbacks all queries and closes the transaction
	 *
	 *     $db->rollback_transaction();
	 *
	 * @param bool $rollback_all:
	 *  true  - rollback everything and close transaction;
	 *  false - rollback only current level
	 *
	 * @return bool
	 */
	public function rollback_transaction($rollback_all = true)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($rollback_all), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ($this->_transaction_depth > 0)
		{
			if($rollback_all or $this->_transaction_depth == 1)
			{
				if($result = $this->driver_rollback())
				{
					$this->_transaction_depth = 0;
					$this->_in_transaction = false;
				}
			}
			else
			{
				$result = $this->rollback_savepoint($this->_transaction_depth - 1);
				// If savepoint is not supported it is not an error
				isset($result) or $result = true;

				$result and $this->_transaction_depth -- ;
			}
		}
		else
		{
			$result = false;
		}

		return $result;
	}

	/**
	 * Begins a transaction on the driver level
	 *
	 * @return bool
	 */
	abstract protected function driver_start_transaction();

	/**
	 * Commits all pending transactional queries on the driver level
	 *
	 * @return bool
	*/
	abstract protected function driver_commit();

	/**
	 * Rollsback all pending transactional queries on the driver level
	 *
	 * @return bool
	*/
	abstract protected function driver_rollback();

	/**
	 * Sets savepoint of the transaction
	 *
	 * @param string $name name of the savepoint
	 * @return boolean true  - savepoint was set successfully;
	 *                 false - failed to set savepoint;
	 *                 null  - RDBMS does not support savepoints
	 */
	protected function set_savepoint($name)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($name), false)) !== __AM_CONTINUE__) return $__am_res; 
		return null;
	}

	/**
	 * Release savepoint of the transaction
	 *
	 * @param string $name name of the savepoint
	 * @return boolean true  - savepoint was set successfully;
	 *                 false - failed to set savepoint;
	 *                 null  - RDBMS does not support savepoints
	 */
	protected function release_savepoint($name)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($name), false)) !== __AM_CONTINUE__) return $__am_res; 
		return null;
	}

	/**
	 * Rollback savepoint of the transaction
	 *
	 * @param string $name name of the savepoint
	 * @return boolean true  - savepoint was set successfully;
	 *                 false - failed to set savepoint;
	 *                 null  - RDBMS does not support savepoints
	 */
	protected function rollback_savepoint($name)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($name), false)) !== __AM_CONTINUE__) return $__am_res; 
		return null;
	}

	/**
	 * Returns the raw connection object for custom method access
	 *
	 *     $db->connection()->lastInsertId('id');
	 *
	 * @return  resource
	 */
	public function connection()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Make sure the database is connected
		$this->_connection or $this->connect();
		return $this->_connection;
	}

	/**
	 * Returns whether or not we have a valid database connection object
	 *
	 *     $db->has_connection()
	 *
	 * @return  bool
	 */
	public function has_connection()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		// return the status of the connection
		return $this->_connection ? true : false;
	}
}
