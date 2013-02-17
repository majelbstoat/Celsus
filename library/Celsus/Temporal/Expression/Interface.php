<?php

interface Celsus_Temporal_Expression_Interface {

	/**
	 * Determines whether the date specified is included in this temporal expression.
	 *
	 * @param string $date
	 */
	public function includes($date);
}