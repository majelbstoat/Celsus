<?php

class Celsus_View_Stream_Compiled {

	protected static $_protocol = 'view';

	protected $_position = 0;

	protected $_data = null;

	protected $_dataLength = null;

	protected $_stat = null;

	public function stream_open($path, $mode, $options, &$openedPath) {

		// Remove the view:// protocol prefix
		$path = substr($path, strlen(static::$_protocol) + 3);

		$this->_data = file_get_contents($path);

		// Check that the file is readable, if not, fail.
		if (false === $this->_data) {
			$this->_stat = stat($path);
			return false;
		}

		// Matches tags like <?* $code ? >
		$replacements = array();
		if (preg_match_all('/(?<tags><\?\*.+?\?>)/', $this->_data, $matches)) {
			$compiler = new Celsus_View_Compiler();
			foreach ($matches['tags'] as $tag) {
				$section = $tag;
				$section[2] = '=';
				$replacements[$tag] = $compiler->compile($section);
			}
		}

		$this->_data = str_replace(array_keys($replacements), $replacements, $this->_data);
		$this->_dataLength = strlen($this->_data);

		$this->_stat = array(
			'mode' => 0100777,
			'size' => $this->_dataLength
		);

		return true;
	}

	/**
	 * Reads from the stream.
	 */
	public function stream_read($count) {

		$return = substr($this->_data, $this->_position, $count);
		$this->_position += strlen($return);

		return $return;
	}

	/**
	 * Tells the current position in the stream.
	 */
	public function stream_tell() {
		return $this->_position;
	}

	/**
	 * Determines if we are at the end of the stream.
	 */
	public function stream_eof() {
		return $this->_position >= $this->_dataLength;
	}

	/**
	 * Stream statistics.
	 */
	public function stream_stat() {

		return $this->_stat;

	}

	/**
	 * Seek to a specific point in the stream.
	 */
	public function stream_seek($offset, $whence) {

		switch ($whence) {
			case SEEK_SET:
				if ($offset < $this->_dataLength && $offset >= 0) {
					$this->_position = $offset;
					return true;
				} else {
					return false;
				}
				break;

			case SEEK_CUR:
				if ($offset >= 0) {
					$this->_position += $offset;
					return true;
				} else {
					return false;
				}
				break;

			case SEEK_END:
				if ($this->_dataLength + $offset >= 0) {
					$this->_position = $this->_dataLength + $offset;
					return true;
				} else {
					return false;
				}
				break;

			default:
				return false;
		}

	}

	public static function register() {
		stream_wrapper_register(static::$_protocol, get_called_class());
	}
}