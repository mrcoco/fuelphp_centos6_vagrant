<?php
/**
 * Database result wrapper.
 *
 * @package    Fuel/Database
 * @category   Query/Result
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @copyright  (c) 2010-2015 Fuel Development Team
 * @license    http://kohanaphp.com/license
 */

namespace Fuel\Core;

abstract class Database_Result implements \Countable, \Iterator, \SeekableIterator, \ArrayAccess, \Sanitization
{
	/**
	 * @var  string Executed SQL for this result
	 */
	protected $_query;

	/**
	 * @var  resource  $_result raw result resource
	 */
	protected $_result;

	/**
	 * @var  int  $_total_rows total number of rows
	 */
	protected $_total_rows  = 0;

	/**
	 * @var  int  $_current_row  current row number
	 */
	protected $_current_row = 0;

	/**
	 * @var  bool  $_as_object  return rows as an object or associative array
	 */
	protected $_as_object;

	/**
	 * @var  bool  $_sanitization_enabled  If this is a records data will be sanitized on get
	 */
	protected $_sanitization_enabled = false;

	/**
	 * Sets the total number of rows and stores the result locally.
	 *
	 * @param  mixed   $result     query result
	 * @param  string  $sql        SQL query
	 * @param  mixed   $as_object  object
	 */
	public function __construct($result, $sql, $as_object)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($result, $sql, $as_object), false)) !== __AM_CONTINUE__) return $__am_res; 
		// Store the result locally
		$this->_result = $result;

		// Store the SQL locally
		$this->_query = $sql;

		if (is_object($as_object))
		{
			// Get the object class name
			$as_object = get_class($as_object);
		}

		// Results as objects or associative arrays
		$this->_as_object = $as_object;
	}

	/**
	 * Result destruction cleans up all open result sets.
	 *
	 * @return  void
	 */
	abstract public function __destruct();

	/**
	 * Get a cached database result from the current result iterator.
	 *
	 *     $cachable = serialize($result->cached());
	 *
	 * @return  Database_Result_Cached
	 * @since   3.0.5
	 */
	public function cached()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return new \Database_Result_Cached($this->as_array(), $this->_query, $this->_as_object);
	}

	/**
	 * Return all of the rows in the result as an array.
	 *
	 *     // Indexed array of all rows
	 *     $rows = $result->as_array();
	 *
	 *     // Associative array of rows by "id"
	 *     $rows = $result->as_array('id');
	 *
	 *     // Associative array of rows, "id" => "name"
	 *     $rows = $result->as_array('id', 'name');
	 *
	 * @param   string  column for associative keys
	 * @param   string  column for values
	 * @return  array
	 */
	public function as_array($key = null, $value = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($key, $value), false)) !== __AM_CONTINUE__) return $__am_res; 
		$results = array();

		if ($key === null and $value === null)
		{
			// Indexed rows

			foreach ($this as $row)
			{
				$results[] = $row;
			}
		}
		elseif ($key === null)
		{
			// Indexed columns

			if ($this->_as_object)
			{
				foreach ($this as $row)
				{
					$results[] = $row->$value;
				}
			}
			else
			{
				foreach ($this as $row)
				{
					$results[] = $row[$value];
				}
			}
		}
		elseif ($value === null)
		{
			// Associative rows

			if ($this->_as_object)
			{
				foreach ($this as $row)
				{
					$results[$row->$key] = $row;
				}
			}
			else
			{
				foreach ($this as $row)
				{
					$results[$row[$key]] = $row;
				}
			}
		}
		else
		{
			// Associative columns

			if ($this->_as_object)
			{
				foreach ($this as $row)
				{
					$results[$row->$key] = $row->$value;
				}
			}
			else
			{
				foreach ($this as $row)
				{
					$results[$row[$key]] = $row[$value];
				}
			}
		}

		$this->rewind();

		return $results;
	}

	/**
	 * Return the named column from the current row.
	 *
	 *     // Get the "id" value
	 *     $id = $result->get('id');
	 *
	 * @param   string $name    column to get
	 * @param   mixed  $default default value if the column does not exist
	 *
	 * @return  mixed
	 */
	public function get($name, $default = null)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($name, $default), false)) !== __AM_CONTINUE__) return $__am_res; 
		$row = $this->current();

		if ($this->_as_object)
		{
			if (isset($row->$name))
			{
				// sanitize the data if needed
				if ( ! $this->_sanitization_enabled)
				{
					$result = $row->$name;
				}
				else
				{
					$result = \Security::clean($row->$name, null, 'security.output_filter');
				}

				return $result;
			}
		}
		else
		{
			if (isset($row[$name]))
			{
				// sanitize the data if needed
				if ( ! $this->_sanitization_enabled)
				{
					$result = $row[$name];
				}
				else
				{
					$result = \Security::clean($row[$name], null, 'security.output_filter');
				}

				return $result;
			}
		}

		return \Fuel::value($default);
	}

	/**
	 * Implements [Countable::count], returns the total number of rows.
	 *
	 *     echo count($result);
	 *
	 * @return  integer
	 */
	public function count()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->_total_rows;
	}

	/**
	 * Implements [ArrayAccess::offsetExists], determines if row exists.
	 *
	 *     if (isset($result[10]))
	 *     {
	 *         // Row 10 exists
	 *     }
	 *
	 * @param integer $offset
	 *
	 * @return boolean
	 */
	public function offsetExists($offset)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($offset), false)) !== __AM_CONTINUE__) return $__am_res; 
		return ($offset >= 0 and $offset < $this->_total_rows);
	}

	/**
	 * Implements [ArrayAccess::offsetGet], gets a given row.
	 *
	 *     $row = $result[10];
	 *
	 * @param integer $offset
	 *
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($offset), false)) !== __AM_CONTINUE__) return $__am_res; 
		if ( ! $this->seek($offset))
		{
			return null;
		}

		$result = $this->current();

		// sanitize the data if needed
		if ($this->_sanitization_enabled)
		{
			$result = \Security::clean($result, null, 'security.output_filter');
		}

		return $result;
	}

	/**
	 * Implements [ArrayAccess::offsetSet], throws an error.
	 * [!!] You cannot modify a database result.
	 *
	 * @param integer $offset
	 * @param mixed   $value
	 *
	 * @throws  \FuelException
	 */
	final public function offsetSet($offset, $value)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($offset, $value), false)) !== __AM_CONTINUE__) return $__am_res; 
		throw new \FuelException('Database results are read-only');
	}

	/**
	 * Implements [ArrayAccess::offsetUnset], throws an error.
	 * [!!] You cannot modify a database result.
	 *
	 * @param integer $offset
	 *
	 * @throws  \FuelException
	 */
	final public function offsetUnset($offset)
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($offset), false)) !== __AM_CONTINUE__) return $__am_res; 
		throw new \FuelException('Database results are read-only');
	}

	/**
	 * Implements [Iterator::key], returns the current row number.
	 *
	 *     echo key($result);
	 *
	 * @return  integer
	 */
	public function key()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->_current_row;
	}

	/**
	 * Implements [Iterator::next], moves to the next row.
	 *
	 *     next($result);
	 *
	 * @return  $this
	 */
	public function next()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		++$this->_current_row;
		return $this;
	}

	/**
	 * Implements [Iterator::prev], moves to the previous row.
	 *
	 *     prev($result);
	 *
	 * @return  $this
	 */
	public function prev()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		--$this->_current_row;
		return $this;
	}

	/**
	 * Implements [Iterator::rewind], sets the current row to zero.
	 *
	 *     rewind($result);
	 *
	 * @return  $this
	 */
	public function rewind()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_current_row = 0;
		return $this;
	}

	/**
	 * Implements [Iterator::valid], checks if the current row exists.
	 *
	 * [!!] This method is only used internally.
	 *
	 * @return  boolean
	 */
	public function valid()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->offsetExists($this->_current_row);
	}

	/**
	 * Enable sanitization mode in the object
	 *
	 * @return  $this
	 */
	public function sanitize()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_sanitization_enabled = true;

		return $this;
	}

	/**
	 * Disable sanitization mode in the object
	 *
	 * @return  $this
	 */
	public function unsanitize()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		$this->_sanitization_enabled = false;

		return $this;
	}

	/**
	 * Returns the current sanitization state of the object
	 *
	 * @return  bool
	 */
	public function sanitized()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		return $this->_sanitization_enabled;
	}
}
