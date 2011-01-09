<?php

class Celsus_Record_Decorator_Status extends Zend_Form_Decorator_Abstract {

	const DEFAULT_TABLE = 't_active_states';
	
	/**
	 * Render a state item and colour it appropriately, using the lookup from the associated reference.
	 *
	 * Replaces $content entirely with the state.
	 *
	 * @param  string $content
	 * @return string
	 */
	public function render($content) {
		$element = $this->getElement();

		if (!$value = $element->getValue()) {
			return '';
		}
		
		if (!$table = $this->getOption('table')) {
			$table = self::DEFAULT_TABLE;
		}

		$value = Celsus_Lookup::lookupAndCache($table, $value);		
		return '<span class="state-' . $value . '">' . $value . '</span>';
	}	
}
