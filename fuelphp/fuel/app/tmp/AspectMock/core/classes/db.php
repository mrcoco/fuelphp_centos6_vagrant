<?php
/**
 * Database object creation helper methods.
 *
 * @package    Fuel\Database
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */

namespace Fuel\Core;

class DB
{
	// Query types
	const SELECT =  1;
	const INSERT =  2;
	const UPDATE =  3;
	const DELETE =  4;

	public static $query_count = 0;

	/**
	 * Create a new [Database_Query] of the given type.
	 *
	 *     // Create a new SELECT query
	 *     $query = DB::query('SELECT * FROM users');
	 *
	 *     // Create a new DELETE query
	 *     $query = DB::query('DELETE FROM users WHERE id = 5');
	 *
	 * Specifying the type changes the returned result. When using
	 * `DB::SELECT`, a [Database_Query_Result] will be returned.
	 * `DB::INSERT` queries will return the insert id and number of rows.
	 * For all other queries, the number of affected rows is returned.
	 *
	 * @param   integer  type: DB::SELECT, DB::UPDATE, etc
	 * @param   string   SQL statement
	 * @return  Database_Query
	 */
	public static function query($sql, $type = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($sql, $type), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Query($sql, $type);
	}

	/*
	 * Returns the last query
	 *
	 * @return	string	the last query
	 */
	public static function last_query($db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->last_query;
	}

	/*
	 * Returns the DB drivers error info
	 *
	 * @return	mixed	the DB drivers error info
	 */
	public static function error_info($db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->error_info();
	}

	/**
	 * Create a new [Database_Query_Builder_Select]. Each argument will be
	 * treated as a column. To generate a `foo AS bar` alias, use an array.
	 *
	 *     // SELECT id, username
	 *     $query = DB::select('id', 'username');
	 *
	 *     // SELECT id AS user_id
	 *     $query = DB::select(array('id', 'user_id'));
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   ...
	 * @return  Database_Query_Builder_Select
	 */
	public static function select($columns = NULL)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($columns), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Query_Builder_Select(func_get_args());
	}

	/**
	 * Create a new [Database_Query_Builder_Select] from an array of columns.
	 *
	 *     // SELECT id, username
	 *     $query = DB::select_array(array('id', 'username'));
	 *
	 * @param   array   columns to select
	 * @return  Database_Query_Builder_Select
	 */
	public static function select_array(array $columns = NULL)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($columns), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Query_Builder_Select($columns);
	}

	/**
	 * Create a new [Database_Query_Builder_Insert].
	 *
	 *     // INSERT INTO users (id, username)
	 *     $query = DB::insert('users', array('id', 'username'));
	 *
	 * @param   string  table to insert into
	 * @param   array   list of column names or array($column, $alias) or object
	 * @return  Database_Query_Builder_Insert
	 */
	public static function insert($table = NULL, array $columns = NULL)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $columns), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Query_Builder_Insert($table, $columns);
	}

	/**
	 * Create a new [Database_Query_Builder_Update].
	 *
	 *     // UPDATE users
	 *     $query = DB::update('users');
	 *
	 * @param   string  table to update
	 * @return  Database_Query_Builder_Update
	 */
	public static function update($table = NULL)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Query_Builder_Update($table);
	}

	/**
	 * Create a new [Database_Query_Builder_Delete].
	 *
	 *     // DELETE FROM users
	 *     $query = DB::delete('users');
	 *
	 * @param   string  table to delete from
	 * @return  Database_Query_Builder_Delete
	 */
	public static function delete($table = NULL)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Query_Builder_Delete($table);
	}

	/**
	 * Create a new [Database_Expression] which is not escaped. An expression
	 * is the only way to use SQL functions within query builders.
	 *
	 *     $expression = DB::expr('COUNT(users.id)');
	 *
	 * @param   string  expression
	 * @return  Database_Expression
	 */
	public static function expr($string)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($string), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Expression($string);
	}

	/**
	 * Create a new [Database_Expression] containing a quoted identifier. An expression
	 * is the only way to use SQL functions within query builders.
	 *
	 *     $expression = DB::identifier('users.id');	// returns `users`.`id` for MySQL
	 *
	 * @param	string	$string	the string to quote
	 * @param	string	$db		the database connection to use
	 * @return	Database_Expression
	 */
	public static function identifier($string, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($string, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Expression(static::quote_identifier($string, $db));
	}

	/**
	 * Quote a value for an SQL query.
	 *
	 * @param	string	$string	the string to quote
	 * @param	string	$db		the database connection to use
	 * @return	string	the quoted value
	 */
	public static function quote($string, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($string, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		if (is_array($string))
		{
			foreach ($string as $k => $s)
			{
				$string[$k] = static::quote($s, $db);
			}
			return $string;
		}
		return \Database_Connection::instance($db)->quote($string);
	}

	/**
	 * Quotes an identifier so it is ready to use in a query.
	 *
	 * @param	string	$string	the string to quote
	 * @param	string	$db		the database connection to use
	 * @return	string	the quoted identifier
	 */
	public static function quote_identifier($string, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($string, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		if (is_array($string))
		{
			foreach ($string as $k => $s)
			{
				$string[$k] = static::quote_identifier($s, $db);
			}
			return $string;
		}
		return \Database_Connection::instance($db)->quote_identifier($string);
	}

	/**
	 * Quote a database table name and adds the table prefix if needed.
	 *
	 * @param	string	$string	the string to quote
	 * @param	string	$db		the database connection to use
	 * @return	string	the quoted identifier
	 */
	public static function quote_table($string, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($string, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		if (is_array($string))
		{
			foreach ($string as $k => $s)
			{
				$string[$k] = static::quote_table($s, $db);
			}
			return $string;
		}
		return \Database_Connection::instance($db)->quote_table($string);
	}

	/**
	 * Escapes a string to be ready for use in a sql query
	 *
	 * @param	string	$string	the string to escape
	 * @param	string	$db		the database connection to use
	 * @return	string	the escaped string
	 */
	public static function escape($string, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($string, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->escape($string);
	}

	/**
	 * If a table name is given it will return the table name with the configured
	 * prefix.  If not, then just the prefix is returned
	 *
	 * @param   string  $table  the table name to prefix
	 * @param   string  $db     the database connection to use
	 * @return  string  the prefixed table name or the prefix
	 */
	public static function table_prefix($table = null, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->table_prefix($table);
	}

	/**
	 * Lists all of the columns in a table. Optionally, a LIKE string can be
	 * used to search for specific fields.
	 *
	 *     // Get all columns from the "users" table
	 *     $columns = DB::list_columns('users');
	 *
	 *     // Get all name-related columns
	 *     $columns = DB::list_columns('users', '%name%');
	 *
	 * @param   string  table to get columns from
	 * @param   string  column to search for
	 * @param   string  the database connection to use
	 * @return  array
	 */
	public static function list_columns($table = null, $like = null, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $like, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->list_columns($table, $like);
	}

	/**
	 * If a table name is given it will return the table name with the configured
	 * prefix.  If not, then just the prefix is returned
	 *
	 * @param   string  $table  the table name to prefix
	 * @param   string  $db     the database connection to use
	 * @return  string  the prefixed table name or the prefix
	 */
	public static function list_tables($like = null, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($like, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->list_tables($like);
	}

	/**
	 * Returns a normalized array describing the SQL data type
	 *
	 *     DB::datatype('char');
	 *
	 * @param   string  SQL data type
	 * @param   string  db connection
	 * @return  array
	 */
	public static function datatype($type, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($type, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->datatype($type);
	}

		/**
	 * Count the number of records in a table.
	 *
	 *     // Get the total number of records in the "users" table
	 *     $count = DB::count_records('users');
	 *
	 * @param   mixed    table name string or array(query, alias)
	 * @param   string  db connection
	 * @return  integer
	 */
	public static function count_records($table, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->count_records($table);
	}

	/**
	 * Count the number of records in the last query, without LIMIT or OFFSET applied.
	 *
	 *     // Get the total number of records that match the last query
	 *     $count = $db->count_last_query();
	 *
	 * @param   string  db connection
	 * @return  integer
	 */
	public static function count_last_query($db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->count_last_query();
	}

	/**
	 * Set the connection character set. This is called automatically by [static::connect].
	 *
	 *     DB::set_charset('utf8');
	 *
	 * @throws  Database_Exception
	 * @param   string   character set name
	 * @param   string  db connection
	 * @return  void
	 */
	public static function set_charset($charset, $db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($charset, $db), true)) !== __AM_CONTINUE__) return $__am_res; 
		\Database_Connection::instance($db)->set_charset($charset);
	}

	/**
	 * Checks whether a connection is in transaction.
	 *
	 *     DB::in_transaction();
	 *
	 * @param   string  db connection
	 * @return  bool
	 */
	public static function in_transaction($db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->in_transaction();
	}

	/**
	 * Begins a transaction on instance
	 *
	 *     DB::start_transaction();
	 *
	 * @param   string  db connection
	 * @return  bool
	 */
	public static function start_transaction($db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->start_transaction();
	}

	/**
	 * Commits all pending transactional queries
	 *
	 *     DB::commit_transaction();
	 *
	 * @param   string  db connection
	 * @return  bool
	 */
	public static function commit_transaction($db = null)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($db), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->commit_transaction();
	}

	/**
	 * Rollsback pending transactional queries
	 * Rollback to the current level uses SAVEPOINT,
	 * it does not work if current RDBMS does not support them.
	 * In this case system rollsback all queries and closes the transaction
	 *
	 *     DB::rollback_transaction();
	 *
	 * @param   string  $db connection
	 * @param   bool    $rollback_all:
	 *             true  - rollback everything and close transaction;
	 *             false - rollback only current level
	 * @return  bool
	 */
	public static function rollback_transaction($db = null, $rollback_all = true)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($db, $rollback_all), true)) !== __AM_CONTINUE__) return $__am_res; 
		return \Database_Connection::instance($db)->rollback_transaction($rollback_all);
	}

}
