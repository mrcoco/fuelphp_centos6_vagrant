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

abstract class HttpException extends \FuelException
{
	/**
	 * Must return a response object for the handle method
	 *
	 * @return  Response
	 */
	abstract protected function response();

	/**
	 * When this type of exception isn't caught this method is called by
	 * Error::exception_handler() to deal with the problem.
	 */
	public function handle()
	{ if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
		// get the exception response
		$response = $this->response();

		// fire any app shutdown events
		\Event::instance()->trigger('shutdown', '', 'none', true);

		// fire any framework shutdown events
		\Event::instance()->trigger('fuel-shutdown', '', 'none', true);

		// send the response out
		$response->send(true);
	}
}
