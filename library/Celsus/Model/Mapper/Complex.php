<?php
/**
 * Celsus PHP Library
 *
 * @category Celsus
 * @package Celsus_Model
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id$
 */

/**
 * Complex mapper for models which rely on multiple bases from the same repository.
 *
 * @category Celsus
 * @package Celsus_Model
 */
class Celsus_Model_Mapper_Complex extends Celsus_Model_Mapper {

	/**
	 * The underlying object.
	 *
	 * @var Celsus_Model_Base_Interface
	 */
	protected $_bases = null;

	/**
	 * The classes of the base.
	 *
	 * @var string
	 */
	protected $_baseClasses = null;

	/**
	 * The relationship between the underlying bases.
	 *
	 * @var array
	 */
	protected $_peerReferences = null;



}