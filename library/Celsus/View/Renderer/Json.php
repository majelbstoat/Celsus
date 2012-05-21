<?php

class Celsus_View_Renderer_Json {

	/**
	 * Renders a view model as JSON, optionally
	 * wrapping the response in a JSONP callback.
	 *
	 * @param Celsus_View_Model $model
	 */
	public function render(Celsus_View_Model $model) {

		$return = $this->encodeModel($model);

		$callback = $model->getCallback();
		if ($callback) {
			$return = $callback . '(' . $values .')';
		}

		return $return;
	}

	/**
	 * Encodes a view model as JSON, recursively rendering child
	 * models as well.
	 *
	 * @param Celsus_View_Model $model
	 */
	public function encodeModel(Celsus_View_Model $model) {

		$values = $model->getData();

		// Recurse through children.
		foreach ($model->getChildren() as $name => $child) {
			$values[$name] = $this->encodeModel($child);
		}

		return Zend_Json::encode($values);
	}

}