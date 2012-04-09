<?php

interface Celsus_Db_Document_Set_Interface {

	public function getAdapter();

	public function add($document);

}