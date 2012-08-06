<?php

class Celsus_Db_Mock_Adapter_Redis_Server extends Redis {

	protected $_data = array();

	protected $_inPipeline = false;

	protected $_pipelineReturn = array();

	protected function _return($data) {
		if ($this->_inPipeline) {
			$this->_pipelineReturn[] = $data;
			return $this;
		} else {
			return $data;
		}
	}

	public function hGetAll($key) {
		$value = isset($this->_data[$key]) ? $this->_data[$key] : null;
		return $this->_return($value);
	}

	public function hMset($key, $values) {
		if (isset($this->_data[$key])) {
			$this->_data[$key] = array_merge($this->_data[$key], $values);
		} else {
			$this->_data[$key] = $values;
		}
		return $this->_return(true);
	}

	public function hSet($key, $field, $value) {
		$added = 1;
		if (isset($this->_data[$key])) {
			if (isset($this->_data[$key][$field])) {
				$added = 0;
			}
			$this->_data[$key][$field] = $value;
		} else {
			$this->_data[$key] = array($field => $value);
		}
		return $this->_return($added);
	}


	public function incr($key) {
		if (isset($this->_data[$key])) {
			$this->_data[$key]++;
		} else {
			$this->_data[$key] = 1;
		}
		return $this->_return($this->_data[$key]);
	}

	public function multi() {
		$this->_inPipeline = true;
		return $this;
	}

	public function exec() {
		$this->_inPipeline = false;
		$return = $this->_pipelineReturn;
		$this->_pipelineReturn = array();
		return $return;
	}

	public function sAdd($key, $value) {
		$added = true;
		if (isset($this->_data[$key])) {
			if (in_array($value, $this->_data[$key])) {
				$added = false;
			} else {
				$this->_data[$key][] = $value;
			}
		} else {
			$this->_data[$key] = array($value);
		}

		return $this->_return($added);
	}

	public function sCard($key) {
		$value = isset($this->_data[$key]) ? count($this->_data[$key]) : 0;
		return $this->_return($value);
	}

	public function flushDb() {
		$this->_data = array();
		return true;
	}

}