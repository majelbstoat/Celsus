<?php

class Celsus_Mixer_Source_Result extends Celsus_Data_Struct {

	/**
	 * The source that generated this result.
	 *
	 * @var string $source
	 */
	public $source;

	/**
	 * The actual result itself.
	 *
	 * @var mixed $result
	 */
	public $result;

	/**
	 * The internal label of this result.
	 *
	 * All sources within a given group must agree on the label for the same result.
	 *
	 * @var string $label
	 */
	public $label;

	/**
	 * The confidence of the result expressed from 0 - 100.
	 *
	 * @var int $weight;
	 */
	public $weight;

}