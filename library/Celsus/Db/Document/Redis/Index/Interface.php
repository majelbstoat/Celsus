<?php

interface Celsus_Db_Document_Redis_Index_Interface {

	public function update($id, array $config, Redis $pipeline);
}