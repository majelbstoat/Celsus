<?php

/**
 * Handles scheduling rules like "2nd Monday of the Month", or "Last Friday
 * of the month".
 */
class Celsus_Temporal_Expression_DayInMonth implements Celsus_Temporal_Expression_Interface {

	/**
	 * The count specifying which occurence in the month we are interested in. -1
	 * is interpreted to mean 'last', -2, 'second last' etc.
	 *
	 * @var int
	 */
	private $_count;

	/**
	 * The day of the week we are interested in.  The number is stored in ISO-8601
	 * format, where 1 = Monday -> 7 = Sunday.
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
	public function __construct($day, $count) {
		$this->_day = $day;
		$this->_count = $count;
	}

	/**
	 * Determines whether the specified date is the required day of the week.
	 *
	 * @param string $date
	 * @return bool
	 */
	private function _dayMatches($date) {
		return (date('N', strtotime($date)) == $this->_day);
	}

	/**
	 * Determines whether the specified date is the required week of the month.
	 *
	 * @param unknown_type $date
	 * @return unknown
	 */
	private function _weekMatches($date) {
		return ($this->_count > 0) ? $this->_weekFromStartMatches($date) : $this->_weekFromEndMatches($date);
	}

	/**
	 * Determines whether the specified date is the required week of the month,
	 * taken from the beginning of the month.
	 *
	 * @param string $date
	 * @return bool
	 */
	private function _weekFromStartMatches($date) {
		return $this->_weekInMonth(date('j', strtotime($date))) == $this->_count;
	}

	/**
	 * Determines whether the specified date is the required week of the month,
	 * taken from the end of the month.
	 *
	 * @param string $date
	 * @return bool
	 */
	private function _weekFromEndMatches($date) {
		$timestamp = strtotime($date);
		$daysRemaining = date('t', $timestamp) - date('j', $timestamp);
		return $this->_weekInMonth($daysRemaining) == abs($this->_count);
	}

	/**
	 * Given a day of the month, returns what week of the month it is in.
	 *
	 * @param int $day
	 * @return int
	 */
	private function _weekInMonth($day) {
		return ((int) floor(($day - 1) / 7)) + 1;
	}

	/**
	 * Determines whether the date specified is included in this temporal
	 * expression as a specific day in the month.
	 *
	 * @param string $date
	 * @return bool
	 */
	public function includes($date) {
		return $this->_dayMatches($date) && $this->_weekMatches($date);
	}
}