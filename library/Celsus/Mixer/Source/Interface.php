<?php

interface Celsus_Mixer_Source_Interface {

	/**
	 * Returns the array of types valid for this source set.
	 */
	public static function getTypes();

	public static function getSource($type, array $config = array());

	public function getType();

	/**
	 * Yields a group of components that can be mixed together.
	 *
	 * @return Celsus_Mixer_Component_Group
	 */
	public function yield(array $config = array());
}