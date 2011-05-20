<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Debug.php 69 2010-09-08 12:32:03Z jamie $
 */

/**
 * Debug functionality
 *
 * @defgroup Celsus_Debug Celsus Debug
 */

/**
 * Provides useful debugging tools.
 *
 * @ingroup Celsus_Debug
 */
class Celsus_Debug {

	public static function print_r($data) {
		echo "<xmp>" . print_r($data, true) . "</xmp>";
	}
}