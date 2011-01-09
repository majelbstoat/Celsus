<?php

/**
 * Handles scheduling rules like "every 3 months"
 */
class Celsus_Temporal_Expression_MonthsFromStart implements Celsus_Temporal_Expression_Interface {

	/**
	 * The count specifying the interval of months we are interested in.
	 *
	 * @var int
	 */
	private $_count;

	/**
	 * The start date of the sequence.  The number is stored in ISO
	 * format, i.e 2000-12-31.
	 *
	 * @var string
	 */
	private $_start;

	/**
	 * Constructs a new Temporal Expression.
	 *
	 * @param int $day
	 * @param int $count
	 */
	public function __construct($start, $count) {
		$this->_start = $start;
		$this->_count = $count;
	}

	/**
	 * Determines whether the date specified is included in this temporal
	 * expression as a month which is a multiple of the specified interval after
	 * the start date.
	 *
	 * @param string $date
	 * @return bool
	 */
	public function includes($date) {
		// Take the current month, minus the start month, mod the interval.  If it is
		// zero then this date should be included.
		return (0 == ((date('n', strtotime($date)) - date('n', strtotime($this->_start))) % $this->_count));
	}

}

?>