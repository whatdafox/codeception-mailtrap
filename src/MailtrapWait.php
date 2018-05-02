<?php

namespace Codeception\Module;

/**
 * A utility class, designed to help the user to wait until a condition turns true.
 *
 */
class MailtrapWait
{
	/**
	 * @var Mailtrap
	 */
	protected $mailtrap;
	/**
	 * @var int
	 */
	protected $timeout;
	/**
	 * @var int
	 */
	protected $interval;

	/**
	 * MailtrapWait constructor.
	 *
	 * @param Mailtrap $mailtrap
	 * @param null|int $timeout_in_second
	 * @param null|int $interval_in_millisecond
	 */
	public function __construct(Mailtrap $mailtrap, $timeout_in_second = null, $interval_in_millisecond = null)
	{
		$this->mailtrap = $mailtrap;
		$this->timeout = isset($timeout_in_second) ? $timeout_in_second : 30;
		$this->interval = $interval_in_millisecond ?: 250;
	}

	/**
	 * Calls the function provided with the driver as an argument until the return value is not falsey.
	 *
	 * @param callable $function
	 * @param string $message
	 *
	 * @throws \Exception
	 * @return mixed The return value of $function
	 */
	public function until($function, $message = '')
	{
		$end = microtime(true) + $this->timeout;
		$last_exception = null;

		while ( $end > microtime( true ) ) {
			try {
				$ret_val = call_user_func( $function, $this->mailtrap );
				if ( $ret_val ) {
					return $ret_val;
				}
			} catch ( \Exception $e ) {
				$last_exception = $e;
			}
			usleep( $this->interval * 1000 );
		}

		if ( $last_exception ) {
			throw $last_exception;
		}

		throw new \Exception( $message );
	}
}
