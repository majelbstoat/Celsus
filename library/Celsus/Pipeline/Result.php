<?php

class Celsus_Pipeline_Result extends Celsus_Data_Struct implements Celsus_Pipeline_Result_Interface {

	/**
	 * The operations that took place on this result.
	 *
	 * @var array $operations
	 */
	public $operations = array();

	public function noteOperation($operation) {
		array_unshift($this->operations, $operation);
	}
}