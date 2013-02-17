<?php

/**
 * Handles scheduling rules like "11th day of the month".
 */
class Celsus_Temporal_Expression_DayOfMonth implements Celsus_Temporal_Expression_Interface {

	/**
	 * The day of the month we are interested in.  If the day is less than zero,
	 * it is interpreted as being from the end of the month.
	 *
	 * @var int
	 */
	private $_day;

	/**
	 * Constructs a new Temporal Expression.
	 *
	 * @param int $day
	 * @param int $count
	 */
	public function __construct($day) {
		$this->_day = $day;
	}

	/**
	 * Determines whether the date specified is included in this temporal
	 * expression as a specific day of the month.
	 *
	 * @param string $date
	 * @return bool
	 */
	public function includes($date) {
		return $this->_day > 0 ? $this->_fromStartOfMonth($date) : $this->_fromEndOfMonth($date);
	}

	/**
	 * Calculates if the supplied date is the correct day of the month.
	 *
	 * @param string $date
	 * @return bool
	 */
	private function _fromStartOfMonth($date) {
		return $this->_day == date('j', strtotime($date));
	}

	/**
	 * Calculates if the supplied date is the correct number of days from the
	 * end of the month.
	 *
	 * @param string $date
	 * @return bool
	 */
	private function _fromEndOfMonth($date) {
		$timestamp = strtotime($date);
		return ((date('t', $timestamp) - date('j', $timestamp)) + 1) == abs($this->_day);
	}
}