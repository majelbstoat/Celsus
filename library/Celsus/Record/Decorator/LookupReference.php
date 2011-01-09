<?php

class Celsus_Record_Decorator_LookupReference extends Zend_Form_Decorator_Abstract {

	/**
	 * Render an item, using the lookup from the associated reference.
	 *
	 * Replaces $content entirely from currently set element.
	 *
	 * @param  string $content
	 * @return string
	 */
	public function render($content) {
		$element = $this->getElement();

		if (!$value = $element->getValue()) {
			return '';
		}
		
		if (!$referenced = $this->getOption('referenced')) {
			$name = $element->getName();
			throw new Celsus_Exception("No reference specified for rendering $name.");
		}
		
		list($table, $field) = (is_array($referenced)) ? $referenced : array($referenced, Celsus_Lookup::DEFAULT_COLUMN);
		
		if ($this->getOption('cacheLookup')) {
			$value = Celsus_Lookup::lookupAndCache($table, $value, $field);
		} else {
			$value = Celsus_Lookup::lookup($table, $value, $field);
		}
		
		return $value;
	}
}
