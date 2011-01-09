<?php

/**
 * Default filter, performs no filtering.
 *
 * @author jamest
 *
 */
class Celsus_Data_Filter_Default implements Celsus_Data_Filter_Interface {

	/**
	 * Returns all the supplied fields, as they are all readable with no filtering.
	 *
	 * @param Celsus_Data_Interface $object
	 * @param array $fields
	 * @return array
	 */
	public static function filterReadable(Celsus_Data_Interface $object, $fields)
	{
		return $fields;
	}

	/**
	 * Returns all the supplied fields, as they are all writeable with no filtering.
	 *
	 * @param Celsus_Data_Interface $object
	 * @param array $fields
	 * @return array
	 */
	public static function filterWriteable(Celsus_Data_Interface $object, $fields)
    {
		return $fields;
	}
}