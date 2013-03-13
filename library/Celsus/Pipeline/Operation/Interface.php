<?php

interface Celsus_Pipeline_Operation_Interface {

	/**
	 * @param Celsus_Pipeline_Result_Interface $results
	 * @return Celsus_Pipeline_Result_Interface
	 */
	public function process(Celsus_Pipeline_Result_Interface $results);

	public function setPipeline(Celsus_Pipeline $pipeline);
}