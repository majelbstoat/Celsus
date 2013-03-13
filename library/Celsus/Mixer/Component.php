<?php

class Celsus_Mixer_Component extends Celsus_Pipeline_Result {

	/**
	 * The confidence of the result expressed from 0 - 100.
	 *
	 * @var int $weight
	 */
	public $confidence = null;

	/**
	 * The internal label of this result.
	 *
	 * All sources within a given group must agree on the label for the same result.
	 *
	 * @var string $label
	 */
	public $label = null;

	/**
	 * The actual result itself.
	 *
	 * @var mixed $result
	 */
	public $result = null;

	/**
	 * The sources that generated this result.
	 *
	 * @var array $sources
	 */
	public $sources = array();


}