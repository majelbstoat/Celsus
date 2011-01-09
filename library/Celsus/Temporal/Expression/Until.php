<?php

/**
 * Handles scheduling rules like "until 26th June 2009"
 */
class Celsus_Temporal_Expression_Until implements Celsus_Temporal_Expression_Interface {

	/**
	 * The date at which this expression will become false.  The number is stored
	 * in ISO format, i.e 2000-12-31.
	 *
	 * @var string
	 */
	private $_until;

	/**
	 * Constructs a new Temporal Expression.
	 *
	 * @param string $until
	 */
	public function __construct($until) {
		$this->_until = $until;
	}

	/**
	 * Determines whether the date specified is included in this temporal
	 * expression as a date which is before the specified final date.
	 *
	 * @param string $date
	 * @return bool
	 */
	public function includes($date) {
		// Simple test, check that supplied date is less than until date.
		return (strtotime($this->_until) > strtotime($date));
	}

}

?>