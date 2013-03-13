<?php

class Celsus_Protobuf_Codec_Reader_Binary {

	protected $_resource = null;

	public function __construct($data) {
		if (is_resource($data)) {
			$this->_resource = $data;
		} else {
			$this->_resource = fopen("data://text/plain," . urlencode($data), "rb");
		}
	}







}