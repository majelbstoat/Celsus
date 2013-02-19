<?php

interface Celsus_Mixer_Source_Interface {

	/**
	 * Returns the array of types valid for this source set.
	 */
	public static function getTypes();

	public static function getSource($type);

	/**
	 * Returns back an array of Celsus_Mixer_Source_Result objects.
	 *
	 * @param integer $maximum The maximum number of results to return.  Some
	 * non-deterministic sources cannot guarantee the exact amount of results they
	 * can return in a timely manner, so we ask for maximums instead of counts.
	 */
	public function yield($maximum);
}