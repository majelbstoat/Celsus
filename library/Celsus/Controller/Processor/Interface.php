<?php

interface Celsus_Controller_Processor_Interface {

	public function __construct(Celsus_Controller_Common $actionController);

	public function getData();

	public function record(Celsus_Model $record);

	public function template(Celsus_Model $record);

	public function success(Celsus_Model $record);

	public function error(Celsus_Model $record, $message);

	public function invalid(Celsus_Model $record);

}