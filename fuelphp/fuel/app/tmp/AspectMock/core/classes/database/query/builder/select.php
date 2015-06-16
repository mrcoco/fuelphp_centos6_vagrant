<?php
/**
 * Database query builder for SELECT statements.
 *
 * @package    Fuel/Database
 * @category   Query
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */

namespace Fuel\Core;

class Database_Query_Builder_Select extends \Database_Query_Builder_Where
{
	/**
	 * @var array  $_select  columns to select
	 */
	protected $_select = array();

	/**
	 * @var bool  $_distinct  whether to select distinct values
	 */
	protected $_distinct = false;

	/**
	 * @var array  $_from  table name
	 */
	protected $_from = array();

	/**
	 * @var array  $_join  join objects
	 */
	protected $_join = array();

	/**
	 * @var array  $_group_by  group by clauses
	 */
	protected $_group_by = array();

	/**
	 * @var array  $_having  having clauses
	 */
	protected $_having = array();

	/**
	 * @var integer  $_offset  offset
	 */
	protected $_offset = null;

	/**
	 * @var  Database_Query_Builder_Join  $_last_join  last join statement
	 */
	protected $_last_join;

	/**
	 * Sets the initial columns to select from.
	 *
	 * @param  array  $columns  column list
	 */
	public function __construct(array $columns = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($columns), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ( ! empty($columns))
		{
			// Set the initial columns
			$this->_select = $columns;
		}

		// Start the query with no actual SQL statement
		parent::__construct('', \DB::SELECT);
	}

	/**
	 * Enables or disables selecting only unique columns using "SELECT DISTINCT"
	 *
	 * @param   boolean  $value  enable or disable distinct columns
	 * @return  $this
	 */
	public function distinct($value = true)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($value), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_distinct = (bool) $value;

		return $this;
	}

	/**
	 * Choose the columns to select from.
	 *
	 * @param   mixed  $columns  column name or array($column, $alias) or object
	 * @param   ...
	 *
	 * @return  $this
	 */
	public function select($columns = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($columns), false)) !== __AM_CONTINUE__) return $__am_res; 
		$columns = func_get_args();

		$this->_select = array_merge($this->_select, $columns);

		return $this;
	}

	/**
	 * Choose the columns to select from, using an array.
	 *
	 * @param   array  $columns  list of column names or aliases
	 * @param   bool   $reset    if true, don't merge but overwrite
	 *
	 * @return  $this
	 */
	public function select_array(array $columns, $reset = false)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($columns, $reset), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_select = $reset ? $columns : array_merge($this->_select, $columns);

		return $this;
	}

	/**
	 * Choose the tables to select "FROM ..."
	 *
	 * @param   mixed  $tables  table name or array($table, $alias)
	 * @param   ...
	 *
	 * @return  $this
	 */
	public function from($tables)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($tables), false)) !== __AM_CONTINUE__) return $__am_res; 
		$tables = func_get_args();

		$this->_from = array_merge($this->_from, $tables);

		return $this;
	}

	/**
	 * Adds addition tables to "JOIN ...".
	 *
	 * @param   mixed   $table  column name or array($column, $alias)
	 * @param   string  $type   join type (LEFT, RIGHT, INNER, etc)
	 *
	 * @return  $this
	 */
	public function join($table, $type = NULL)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($table, $type), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_join[] = $this->_last_join = new \Database_Query_Builder_Join($table, $type);

		return $this;
	}

	/**
	 * Adds "ON ..." conditions for the last created JOIN statement.
	 *
	 * @param   mixed   $c1  column name or array($column, $alias) or object
	 * @param   string  $op  logic operator
	 * @param   mixed   $c2  column name or array($column, $alias) or object
	 *
	 * @return  $this
	 */
	public function on($c1, $op, $c2)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($c1, $op, $c2), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_last_join->on($c1, $op, $c2);

		return $this;
	}

	/**
	 * Adds "AND ON ..." conditions for the last created JOIN statement.
	 *
	 * @param   mixed   $c1  column name or array($column, $alias) or object
	 * @param   string  $op  logic operator
	 * @param   mixed   $c2  column name or array($column, $alias) or object
	 *
	 * @return  $this
	 */
	public function and_on($c1, $op, $c2)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($c1, $op, $c2), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_last_join->and_on($c1, $op, $c2);

		return $this;
	}

	/**
	 * Adds "OR ON ..." conditions for the last created JOIN statement.
	 *
	 * @param   mixed   $c1  column name or array($column, $alias) or object
	 * @param   string  $op  logic operator
	 * @param   mixed   $c2  column name or array($column, $alias) or object
	 *
	 * @return  $this
	 */
	public function or_on($c1, $op, $c2)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($c1, $op, $c2), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_last_join->or_on($c1, $op, $c2);

		return $this;
	}

	/**
	 * Creates a "GROUP BY ..." filter.
	 *
	 * @param   mixed  $columns  column name or array($column, $column) or object
	 * @param   ...
	 *
	 * @return  $this
	 */
	public function group_by($columns)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($columns), false)) !== __AM_CONTINUE__) return $__am_res; 
		$columns = func_get_args();

		foreach($columns as $idx => $column)
		{
			// if an array of columns is passed, flatten it
			if (is_array($column))
			{
				foreach($column as $c)
				{
					$columns[] = $c;
				}
				unset($columns[$idx]);
			}
		}

		$this->_group_by = array_merge($this->_group_by, $columns);

		return $this;
	}

	/**
	 * Alias of and_having()
	 *
	 * @param   mixed  $column column name or array($column, $alias) or object
	 * @param   string $op     logic operator
	 * @param   mixed  $value  column value
	 *
	 * @return  $this
	 */
	public function having($column, $op = null, $value = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($column, $op, $value), false)) !== __AM_CONTINUE__) return $__am_res; 
		return call_fuel_func_array(array($this, 'and_having'), func_get_args());
	}

	/**
	 * Creates a new "AND HAVING" condition for the query.
	 *
	 * @param   mixed  $column column name or array($column, $alias) or object
	 * @param   string $op     logic operator
	 * @param   mixed  $value  column value
	 *
	 * @return  $this
	 */
	public function and_having($column, $op = null, $value = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($column, $op, $value), false)) !== __AM_CONTINUE__) return $__am_res; 
		if($column instanceof \Closure)
		{
			$this->and_having_open();
			$column($this);
			$this->and_having_close();
			return $this;
		}

		if(func_num_args() === 2)
		{
			$value = $op;
			$op = '=';
		}

		$this->_having[] = array('AND' => array($column, $op, $value));

		return $this;
	}

	/**
	 * Creates a new "OR HAVING" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 *
	 * @return  $this
	 */
	public function or_having($column, $op = null, $value = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($column, $op, $value), false)) !== __AM_CONTINUE__) return $__am_res; 
		if($column instanceof \Closure)
		{
			$this->or_having_open();
			$column($this);
			$this->or_having_close();
			return $this;
		}

		if(func_num_args() === 2)
		{
			$value = $op;
			$op = '=';
		}

		$this->_having[] = array('OR' => array($column, $op, $value));

		return $this;
	}

	/**
	 * Alias of and_having_open()
	 *
	 * @return  $this
	 */
	public function having_open()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->and_having_open();
	}

	/**
	 * Opens a new "AND HAVING (...)" grouping.
	 *
	 * @return  $this
	 */
	public function and_having_open()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_having[] = array('AND' => '(');

		return $this;
	}

	/**
	 * Opens a new "OR HAVING (...)" grouping.
	 *
	 * @return  $this
	 */
	public function or_having_open()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_having[] = array('OR' => '(');

		return $this;
	}

	/**
	 * Closes an open "AND HAVING (...)" grouping.
	 *
	 * @return  $this
	 */
	public function having_close()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->and_having_close();
	}

	/**
	 * Closes an open "AND HAVING (...)" grouping.
	 *
	 * @return  $this
	 */
	public function and_having_close()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_having[] = array('AND' => ')');

		return $this;
	}

	/**
	 * Closes an open "OR HAVING (...)" grouping.
	 *
	 * @return  $this
	 */
	public function or_having_close()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_having[] = array('OR' => ')');

		return $this;
	}

	/**
	 * Start returning results after "OFFSET ..."
	 *
	 * @param   integer  $number  starting result number
	 *
	 * @return  $this
	 */
	public function offset($number)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($number), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_offset = (int) $number;

		return $this;
	}

	/**
	 * Compile the SQL query and return it.
	 *
	 * @param   mixed  $db  Database_Connection instance or instance name
	 *
	 * @return  string
	 */
	public function compile($db = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($db), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ( ! $db instanceof \Database_Connection)
		{
			// Get the database instance
			$db = $this->_connection ?: \Database_Connection::instance($db);
		}

		// Callback to quote identifiers
		$quote_ident = array($db, 'quote_identifier');

		// Callback to quote tables
		$quote_table = array($db, 'quote_table');

		// Start a selection query
		$query = 'SELECT ';

		if ($this->_distinct === TRUE)
		{
			// Select only unique results
			$query .= 'DISTINCT ';
		}

		if (empty($this->_select))
		{
			// Select all columns
			$query .= '*';
		}
		else
		{
			// Select all columns
			$query .= implode(', ', array_unique(array_map($quote_ident, $this->_select)));
		}

		if ( ! empty($this->_from))
		{
			// Set tables to select from
			$query .= ' FROM '.implode(', ', array_unique(array_map($quote_table, $this->_from)));
		}

		if ( ! empty($this->_join))
		{
			// Add tables to join
			$query .= ' '.$this->_compile_join($db, $this->_join);
		}

		if ( ! empty($this->_where))
		{
			// Add selection conditions
			$query .= ' WHERE '.$this->_compile_conditions($db, $this->_where);
		}

		if ( ! empty($this->_group_by))
		{
			// Add sorting
			$query .= ' GROUP BY '.implode(', ', array_map($quote_ident, $this->_group_by));
		}

		if ( ! empty($this->_having))
		{
			// Add filtering conditions
			$query .= ' HAVING '.$this->_compile_conditions($db, $this->_having);
		}

		if ( ! empty($this->_order_by))
		{
			// Add sorting
			$query .= ' '.$this->_compile_order_by($db, $this->_order_by);
		}

		if ($this->_limit !== NULL)
		{
			// Add limiting
			$query .= ' LIMIT '.$this->_limit;
		}

		if ($this->_offset !== NULL)
		{
			// Add offsets
			$query .= ' OFFSET '.$this->_offset;
		}

		return $query;
	}

	/**
	 * Reset the query parameters
	 * @return $this
	 */
	public function reset()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_select   = array();
		$this->_from     = array();
		$this->_join     = array();
		$this->_where    = array();
		$this->_group_by = array();
		$this->_having   = array();
		$this->_order_by = array();
		$this->_distinct = false;
		$this->_limit     = null;
		$this->_offset    = null;
		$this->_last_join = null;
		$this->_parameters = array();

		return $this;
	}

}
