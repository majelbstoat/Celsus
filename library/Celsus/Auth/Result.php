<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Represents the result of an authentication request.
 *
 * @ingroup Celsus_Auth
 */
class Celsus_Auth_Result extends Zend_Auth_Result {

	/**
	 * Failure due to a timeout of the authentication service.
	 */
	const FAILURE_TIMEOUT = -5;
}