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
 * DBUtil Class
 *
 * @package		Fuel
 * @category	Core
 * @author		Dan Horrigan
 */
class DBUtil
{
	/**
	 * @var  string  $connection  the database connection (identifier)
	 */
	protected static $connection = null;

	/**
	 * Sets the database connection to use for following DBUtil calls.
	 *
	 * @param  string  $connection  connection name, null for default
	 */
	public static function set_connection($connection)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($connection), true)) !== __AM_CONTINUE__) return $__am_res; 
		if ($connection !== null and ! is_string($connection))
		{
			throw new \FuelException('A connection must be supplied as a string.');
		}

		static::$connection = $connection;
	}

	/**
	 * Creates a database.  Will throw a Database_Exception if it cannot.
	 *
	 * @throws  Fuel\Database_Exception
	 * @param   string  $database       the database name
	 * @param   string  $database       the character set
	 * @param   boolean $if_not_exists  whether to add an IF NOT EXISTS statement.
	 * @param   string  $db             the database connection to use
	 * @return  int     the number of affected rows
	 */
	public static function create_database($database, $charset = null, $if_not_exists = true, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($database, $charset, $if_not_exists, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		$sql = 'CREATE DATABASE';
		$sql .= $if_not_exists ? ' IF NOT EXISTS ' : ' ';

		$charset = static::process_charset($charset, true, $db);

		return \DB::query($sql.\DB::quote_identifier($database, $db ? $db : static::$connection).$charset, 0)->execute($db ? $db : static::$connection);
	}

	/**
	 * Drops a database.  Will throw a Database_Exception if it cannot.
	 *
	 * @throws  Fuel\Database_Exception
	 * @param   string  $database   the database name
	 * @param   string  $db         the database connection to use
	 * @return  int     the number of affected rows
	 */
	public static function drop_database($database, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($database, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \DB::query('DROP DATABASE '.\DB::quote_identifier($database, $db ? $db : static::$connection), 0)->execute($db ? $db : static::$connection);
	}

	/**
	 * Drops a table.  Will throw a Database_Exception if it cannot.
	 *
	 * @throws  Fuel\Database_Exception
	 * @param   string  $table  the table name
	 * @param   string  $db     the database connection to use
	 * @return  int     the number of affected rows
	 */
	public static function drop_table($table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \DB::query('DROP TABLE IF EXISTS '.\DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection), 0)->execute($db ? $db : static::$connection);
	}

	/**
	 * Renames a table.  Will throw a Database_Exception if it cannot.
	 *
	 * @throws  \Database_Exception
	 * @param   string  $table          the old table name
	 * @param   string  $new_table_name the new table name
	 * @param   string  $db             the database connection to use
	 * @return  int     the number of affected
	 */
	public static function rename_table($table, $new_table_name, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $new_table_name, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \DB::query('RENAME TABLE '.\DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection).' TO '.\DB::quote_identifier(\DB::table_prefix($new_table_name, $db ? $db : static::$connection), $db ? $db : static::$connection), 0)->execute($db ? $db : static::$connection);
	}

	/**
	 * Creates a table.
	 *
	 * @throws  \Database_Exception
	 * @param   string  $table          the table name
	 * @param   array   $fields         the fields array
	 * @param   array   $primary_keys   an array of primary keys
	 * @param   boolean $if_not_exists  whether to add an IF NOT EXISTS statement.
	 * @param   string  $engine         storage engine overwrite
	 * @param   string  $charset        default charset overwrite
	 * @param   array   $foreign_keys   an array of foreign keys
	 * @param   string  $db             the database connection to use
	 * @return  int     number of affected rows.
	 */
	public static function create_table($table, $fields, $primary_keys = array(), $if_not_exists = true, $engine = false, $charset = null, $foreign_keys = array(), $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $fields, $primary_keys, $if_not_exists, $engine, $charset, $foreign_keys, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		$sql = 'CREATE TABLE';

		$sql .= $if_not_exists ? ' IF NOT EXISTS ' : ' ';

		$sql .= \DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection).' (';
		$sql .= static::process_fields($fields, '', $db);
		if ( ! empty($primary_keys))
		{
			$key_name = \DB::quote_identifier(implode('_', $primary_keys), $db ? $db : static::$connection);
			$primary_keys = \DB::quote_identifier($primary_keys, $db ? $db : static::$connection);
			$sql .= ",\n\tPRIMARY KEY ";
			if (strtolower(\Config::get('db.'.($db ? $db : \Config::get('db.active')).'.type')) === 'pdo')
			{
				$dsn = \Config::get('db.'.($db ? $db : \Config::get('db.active')).'.connection.dsn');
				$_dsn_find_collon = strpos($dsn, ':');
				if (($_dsn_find_collon ? substr($dsn, 0, $_dsn_find_collon) : null) !== 'sqlite')
				{
					$sql .= $key_name;
				}
			}
			$sql .= $key_name." (" . implode(', ', $primary_keys) . ")";
		}

		empty($foreign_keys) or $sql .= static::process_foreign_keys($foreign_keys, $db);

		$sql .= "\n)";
		$sql .= ($engine !== false) ? ' ENGINE = '.$engine.' ' : '';
		$sql .= static::process_charset($charset, true, $db).";";

		return \DB::query($sql, 0)->execute($db ? $db : static::$connection);
	}

	/**
	 * Adds fields to a table a table.  Will throw a Database_Exception if it cannot.
	 *
	 * @throws  Fuel\Database_Exception
	 * @param   string  $table   the table name
	 * @param   array   $fields  the new fields
	 * @param   string  $db      the database connection to use
	 * @return  int     the number of affected
	 */
	public static function add_fields($table, $fields, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $fields, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::alter_fields('ADD', $table, $fields, $db);
	}

	/**
	 * Modifies fields in a table.  Will throw a Database_Exception if it cannot.
	 *
	 * @throws  Fuel\Database_Exception
	 * @param   string  $table    the table name
	 * @param   array   $fields   the modified fields
	 * @return  int     the number of affected
	 */
	public static function modify_fields($table, $fields, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $fields, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::alter_fields('MODIFY', $table, $fields, $db);
	}

	/**
	 * Drops fields from a table a table.  Will throw a Database_Exception if it cannot.
	 *
	 * @throws  Fuel\Database_Exception
	 * @param   string        $table   the table name
	 * @param   string|array  $fields  the fields
	 * @param   string        $db      the database connection to use
	 * @return  int           the number of affected
	 */
	public static function drop_fields($table, $fields, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $fields, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::alter_fields('DROP', $table, $fields, $db);
	}

	protected static function alter_fields($type, $table, $fields, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($type, $table, $fields, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		$sql = 'ALTER TABLE '.\DB::quote_identifier(\DB::table_prefix($table, $db ?: static::$connection), $db ?: static::$connection).' ';

		if ($type === 'DROP')
		{
			if ( ! is_array($fields))
			{
				$fields = array($fields);
			}

			$drop_fields = array();
			foreach ($fields as $field)
			{
				$drop_fields[] = 'DROP '.\DB::quote_identifier($field, $db ?: static::$connection);
			}
			$sql .= implode(', ', $drop_fields);
		}
		else
		{
			$use_brackets = ! in_array($type, array('ADD', 'CHANGE', 'MODIFY'));
			$use_brackets and $sql .= $type.' ';
			$use_brackets and $sql .= '(';
			$sql .= static::process_fields($fields, (( ! $use_brackets) ? $type.' ' : ''), $db);
			$use_brackets and $sql .= ')';
		}

		return \DB::query($sql, 0)->execute($db ?: static::$connection);
	}

	/**
	 * Creates an index on that table.
	 *
	 * @access  public
	 * @static
	 * @param   string  $table
	 * @param   string  $index_name
	 * @param   string  $index_columns
	 * @param   string  $index (should be 'unique', 'fulltext', 'spatial' or 'nonclustered')
	 * @param   string  $db    the database connection to use
	 * @return  bool
	 * @author  Thomas Edwards
	 */
	public static function create_index($table, $index_columns, $index_name = '', $index = '', $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $index_columns, $index_name, $index, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		static $accepted_index = array('UNIQUE', 'FULLTEXT', 'SPATIAL', 'NONCLUSTERED', 'PRIMARY');

		// make sure the index type is uppercase
		$index !== '' and $index = strtoupper($index);

		if (empty($index_name))
		{
			if (is_array($index_columns))
			{
				foreach ($index_columns as $key => $value)
				{
					if (is_numeric($key))
					{
						$index_name .= ($index_name == '' ? '' : '_').$value;
					}
					else
					{
						$index_name .= ($index_name == '' ? '' : '_').str_replace(array('(', ')', ' '), '', $key);
					}
				}
			}
			else
			{
				$index_name = $index_columns;
			}
		}

		if ($index == 'PRIMARY')
		{
			$sql = 'ALTER TABLE ';
			$sql .= \DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection);
			$sql .= ' ADD PRIMARY KEY ';
			if (is_array($index_columns))
			{
				$columns = '';
				foreach ($index_columns as $key => $value)
				{
					if (is_numeric($key))
					{
						$columns .= ($columns=='' ? '' : ', ').\DB::quote_identifier($value, $db ? $db : static::$connection);
					}
					else
					{
						$columns .= ($columns=='' ? '' : ', ').\DB::quote_identifier($key, $db ? $db : static::$connection).' '.strtoupper($value);
					}
				}
				$sql .= ' ('.$columns.')';
			}
		}
		else
		{
			$sql = 'CREATE ';

			$index !== '' and $sql .= (in_array($index, $accepted_index)) ? $index.' ' : '';

			$sql .= 'INDEX ';
			$sql .= \DB::quote_identifier($index_name, $db ? $db : static::$connection);
			$sql .= ' ON ';
			$sql .= \DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection);
			if (is_array($index_columns))
			{
				$columns = '';
				foreach ($index_columns as $key => $value)
				{
					if (is_numeric($key))
					{
						$columns .= ($columns=='' ? '' : ', ').\DB::quote_identifier($value, $db ? $db : static::$connection);
					}
					else
					{
						$columns .= ($columns=='' ? '' : ', ').\DB::quote_identifier($key, $db ? $db : static::$connection).' '.strtoupper($value);
					}
				}
				$sql .= ' ('.$columns.')';
			}
			else
			{
				$sql .= ' ('.\DB::quote_identifier($index_columns, $db ? $db : static::$connection).')';
			}
		}

		return \DB::query($sql, 0)->execute($db ? $db : static::$connection);
	}

	/**
	 * Drop an index from a table.
	 *
	 * @access  public
	 * @static
	 * @param   string  $table
	 * @param   string  $index_name
	 * @param   string  $db          the database connection to use
	 * @return  bool
	 * @author  Thomas Edwards
	 */
	public static function drop_index($table, $index_name, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $index_name, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		if (strtoupper($index_name) == 'PRIMARY')
		{
			$sql = 'ALTER TABLE '.\DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection);
			$sql .= ' DROP PRIMARY KEY';
		}
		else
		{
			$sql = 'DROP INDEX '.\DB::quote_identifier($index_name, $db ? $db : static::$connection);
			$sql .= ' ON '.\DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection);
		}

		return \DB::query($sql, 0)->execute($db ? $db : static::$connection);
	}

	protected static function process_fields($fields, $prefix = '', $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($fields, $prefix, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		$sql_fields = array();

		foreach ($fields as $field => $attr)
		{
			$attr = array_change_key_case($attr, CASE_UPPER);
			$_prefix = $prefix;
			if(array_key_exists('NAME', $attr) and $field !== $attr['NAME'] and $_prefix === 'MODIFY ')
			{
				$_prefix = 'CHANGE ';
			}
			$sql = "\n\t".$_prefix;
			$sql .= \DB::quote_identifier($field, $db ? $db : static::$connection);
			$sql .= (array_key_exists('NAME', $attr) and $attr['NAME'] !== $field) ? ' '.\DB::quote_identifier($attr['NAME'], $db ? $db : static::$connection).' ' : '';
			$sql .= array_key_exists('TYPE', $attr) ? ' '.$attr['TYPE'] : '';

			if(array_key_exists('CONSTRAINT', $attr))
			{
				if(is_array($attr['CONSTRAINT']))
				{
					$sql .= "(";
					foreach($attr['CONSTRAINT'] as $constraint)
					{
						$sql .= (is_string($constraint) ? "'".$constraint."'" : $constraint).", ";
					}
					$sql = rtrim($sql, ', '). ")";
				}
				else
				{
					$sql .= '('.$attr['CONSTRAINT'].')';
				}
			}

			$sql .= array_key_exists('CHARSET', $attr) ? static::process_charset($attr['CHARSET'], false, $db) : '';

			if (array_key_exists('UNSIGNED', $attr) and $attr['UNSIGNED'] === true)
			{
				$sql .= ' UNSIGNED';
			}

			if(array_key_exists('DEFAULT', $attr))
			{
				$sql .= ' DEFAULT '.(($attr['DEFAULT'] instanceof \Database_Expression) ? $attr['DEFAULT']  : \DB::quote($attr['DEFAULT'], $db ? $db : static::$connection));
			}

			if(array_key_exists('NULL', $attr) and $attr['NULL'] === true)
			{
				$sql .= ' NULL';
			}
			else
			{
				$sql .= ' NOT NULL';
			}

			if (array_key_exists('AUTO_INCREMENT', $attr) and $attr['AUTO_INCREMENT'] === true)
			{
				$sql .= ' AUTO_INCREMENT';
			}

			if (array_key_exists('PRIMARY_KEY', $attr) and $attr['PRIMARY_KEY'] === true)
			{
				$sql .= ' PRIMARY KEY';
			}

			if (array_key_exists('COMMENT', $attr))
			{
				$sql .= ' COMMENT '.\DB::escape($attr['COMMENT'], $db ? $db : static::$connection);
			}

			if (array_key_exists('FIRST', $attr) and $attr['FIRST'] === true)
			{
				$sql .= ' FIRST';
			}
			elseif (array_key_exists('AFTER', $attr) and strval($attr['AFTER']))
			{
				$sql .= ' AFTER '.\DB::quote_identifier($attr['AFTER'], $db ? $db : static::$connection);
			}

			$sql_fields[] = $sql;
		}

		return \implode(',', $sql_fields);
	}

	/**
	 * Formats the default charset.
	 *
	 * @param    string    $charset       the character set
	 * @param    bool      $is_default    whether to use default
	 * @param    string    $db            the database name in the config
	 * @param    string    $collation     the collating sequence to be used
	 * @return   string    the formated charset sql
	 */
	protected static function process_charset($charset = null, $is_default = false, $db = null, $collation = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($charset, $is_default, $db, $collation), true)) !== __AM_CONTINUE__) return $__am_res; 
		$charset or $charset = \Config::get('db.'.($db ? $db : \Config::get('db.active')).'.charset', null);

		if (empty($charset))
		{
			return '';
		}

		$collation or $collation = \Config::get('db.'.($db ? $db : \Config::get('db.active')).'.collation', null);

		if (empty($collation) and ($pos = stripos($charset, '_')) !== false)
		{
			$collation = $charset;
			$charset = substr($charset, 0, $pos);
		}

		$charset = 'CHARACTER SET '.$charset;

		if ($is_default)
		{
			$charset = 'DEFAULT '.$charset;
		}

		if ( ! empty($collation))
		{
			if ($is_default)
			{
				$charset .= ' DEFAULT';
			}
			$charset .= ' COLLATE '.$collation;
		}

		return $charset;
	}

	/**
	 * Adds a single foreign key to a table
	 *
	 * @param   string  $table          the table name
	 * @param   array   $foreign_key    a single foreign key
	 * @return  int     number of affected rows
	 */
	public static function add_foreign_key($table, $foreign_key)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $foreign_key), true)) !== __AM_CONTINUE__) return $__am_res; 
		if ( ! is_array($foreign_key))
		{
			throw new \InvalidArgumentException('Foreign key for add_foreign_key() must be specified as an array');
		}

		$sql = 'ALTER TABLE ';
		$sql .= \DB::quote_identifier(\DB::table_prefix($table, static::$connection), static::$connection).' ';
		$sql .= 'ADD ';
		$sql .= ltrim(static::process_foreign_keys(array($foreign_key), static::$connection), ',');

		return \DB::query($sql, 0)->execute(static::$connection);
	}

	/**
	 * Drops a foreign key from a table
	 *
	 * @param   string  $table      the table name
	 * @param   string  $fk_name    the foreign key name
	 * @return  int     number of affected rows
	 */
	public static function drop_foreign_key($table, $fk_name)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $fk_name), true)) !== __AM_CONTINUE__) return $__am_res; 
		$sql = 'ALTER TABLE ';
		$sql .= \DB::quote_identifier(\DB::table_prefix($table, static::$connection), static::$connection).' ';
		$sql .= 'DROP FOREIGN KEY '.\DB::quote_identifier($fk_name, static::$connection);

		return \DB::query($sql, 0)->execute(static::$connection);
	}

	/**
	 * Returns string of foreign keys
	 *
	 * @param   array   $foreign_keys  Array of foreign key rules
	 * @param   string  $db            the database connection to use
	 * @return  string  the formated foreign key string
	 */
	public static function process_foreign_keys($foreign_keys, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($foreign_keys, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		if ( ! is_array($foreign_keys))
		{
			throw new \Database_Exception('Foreign keys on create_table() must be specified as an array');
		}

		$fk_list = array();

		foreach($foreign_keys as $definition)
		{
			// some sanity checks
			if (empty($definition['key']))
			{
				throw new \Database_Exception('Foreign keys on create_table() must specify a foreign key name');
			}
			if ( empty($definition['reference']))
			{
				throw new \Database_Exception('Foreign keys on create_table() must specify a foreign key reference');
			}
			if (empty($definition['reference']['table']) or empty($definition['reference']['column']))
			{
				throw new \Database_Exception('Foreign keys on create_table() must specify a reference table and column name');
			}

			$sql = '';
			! empty($definition['constraint']) and $sql .= " CONSTRAINT ".\DB::quote_identifier($definition['constraint'], $db ? $db : static::$connection);
			$sql .= " FOREIGN KEY (".\DB::quote_identifier($definition['key'], $db ? $db : static::$connection).')';
			$sql .= " REFERENCES ".\DB::quote_identifier(\DB::table_prefix($definition['reference']['table'], $db ? $db : static::$connection), $db ? $db : static::$connection).' (';
			if (is_array($definition['reference']['column']))
			{
				$sql .= implode(', ', \DB::quote_identifier($definition['reference']['column'], $db ? $db : static::$connection));
			}
			else
			{
				$sql .= \DB::quote_identifier($definition['reference']['column'], $db ? $db : static::$connection);
			}
			$sql .= ')';
			! empty($definition['on_update']) and $sql .= " ON UPDATE ".$definition['on_update'];
			! empty($definition['on_delete']) and $sql .= " ON DELETE ".$definition['on_delete'];

			$fk_list[] = "\n\t".ltrim($sql);
		}

		return ', '.implode(',', $fk_list);
	}

	/**
	 * Truncates a table.
	 *
	 * @throws  Fuel\Database_Exception
	 * @param   string  $table  the table name
	 * @param   string  $db     the database connection to use
	 * @return  int     the number of affected rows
	 */
	public static function truncate_table($table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \DB::query('TRUNCATE TABLE '.\DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection), \DB::DELETE)
			->execute($db ? $db : static::$connection);
	}

	/**
	 * Analyzes a table.
	 *
	 * @param   string  $table  the table name
	 * @param   string  $db     the database connection to use
	 * @return  bool    whether the table is OK
	 */
	public static function analyze_table($table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::table_maintenance('ANALYZE TABLE', $table, $db);
	}

	/**
	 * Checks a table.
	 *
	 * @param   string  $table  the table name
	 * @param   string  $db     the database connection to use
	 * @return  bool    whether the table is OK
	 */
	public static function check_table($table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::table_maintenance('CHECK TABLE', $table, $db);
	}

	/**
	 * Optimizes a table.
	 *
	 * @param   string  $table  the table name
	 * @param   string  $db     the database connection to use
	 * @return  bool    whether the table has been optimized
	 */
	public static function optimize_table($table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::table_maintenance('OPTIMIZE TABLE', $table, $db);
	}

	/**
	 * Repairs a table.
	 *
	 * @param   string  $table  the table name
	 * @param   string  $db     the database connection to use
	 * @return  bool    whether the table has been repaired
	 */
	public static function repair_table($table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return static::table_maintenance('REPAIR TABLE', $table, $db);
	}

	/**
	 * Checks if a given table exists.
	 *
	 * @param   string  $table  Table name
	 * @param   string  $db     the database connection to use
	 * @return  bool
	 */
	public static function table_exists($table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		try
		{
			\DB::select()->from($table)->limit(1)->execute($db ? $db : static::$connection);
			return true;
		}
		catch (\Database_Exception $e)
		{
			// check if we have a DB connection at all
			$connection = \Database_Connection::instance($db ? $db : static::$connection)->has_connection();

			// if no connection could be made, re throw the exception
			if ( ! $connection)
			{
				throw $e;
			}

			return false;
		}
	}

	/**
	 * Checks if given field(s) in a given table exists.
	 *
	 * @param   string          $table      Table name
	 * @param   string|array    $columns    columns to check
	 * @param   string          $db         the database connection to use
	 * @return  bool
	 */
	public static function field_exists($table, $columns, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $columns, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		if ( ! is_array($columns))
		{
			$columns = array($columns);
		}

		try
		{
			\DB::select_array($columns)->from($table)->limit(1)->execute($db ? $db : static::$connection);
			return true;
		}
		catch (\Database_Exception $e)
		{
			// check if we have a DB connection at all
			$connection = \Database_Connection::instance($db ? $db : static::$connection)->has_connection();

			// if no connection could be made, re throw the exception
			if ( ! $connection)
			{
				throw $e;
			}

			return false;
		}
	}

	/*
	 * Executes table maintenance. Will throw FuelException when the operation is not supported.
	 *
	 * @throws  FuelException
	 * @param   string  $table  the table name
	 * @param   string  $db     the database connection to use
	 * @return  bool    whether the operation has succeeded
	 */
	protected static function table_maintenance($operation, $table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($operation, $table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		$result = \DB::query($operation.' '.\DB::quote_identifier(\DB::table_prefix($table, $db ? $db : static::$connection), $db ? $db : static::$connection), \DB::SELECT)->execute($db ? $db : static::$connection);
		$type = $result->get('Msg_type');
		$message = $result->get('Msg_text');
		$table = $result->get('Table');

		if ($type === 'status' and in_array(strtolower($message), array('ok', 'table is already up to date')))
		{
			return true;
		}

		// make sure we have a type logger can handle
		if (in_array($type, array('info', 'warning', 'error')))
		{
			$type = strtoupper($type);
		}
		else
		{
			$type = \Fuel::L_INFO;
		}

		logger($type, 'Table: '.$table.', Operation: '.$operation.', Message: '.$result->get('Msg_text'), 'DBUtil::table_maintenance');

		return false;
	}

	/*
	 * Load the db config, the Database_Connection might not have fired jet.
	 *
	 */
	public static function _init()
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
		\Config::load('db', true);
	}

}
