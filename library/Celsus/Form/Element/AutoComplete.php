<?php

class Celsus_Form_Element_AutoComplete extends ZendX_JQuery_Form_Element_AutoComplete {

	public function setMultiOptions($data) {
		if (is_array($data)) {
			// Setting directly from an array of options.
			foreach ($data as $index => $value) {
				$mapped[] = array(
					'label' => $value,
					'value' =>  $index
				);
			}
			$this->setJQueryParam('data', $mapped);
		} else {
			// Setting from a URL.
			$this->setJQueryParam('url', $data);
		}

	}
}
