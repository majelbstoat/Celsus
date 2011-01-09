<?php

abstract class Celsus_Model_Base_DbTable extends Zend_Db_Table_Abstract implements Celsus_Model_Base_Interface {

	/**
	 * The adapter to use for this model.
	 *
	 * @var string
	 */
	protected $_adapter = null;

	/**
	 * The model service that is using this base.
	 *
	 * @var Celsus_Model_Service
	 */
	protected $_service = null;

	/**
	 * The references to other tables, if applicable.
	 *
	 * @var array
	 */
	protected $_lookupReferences = array();

	/**
	 * Default primary key column
	 *
	 * @var string
	 */
	protected $_primary = 'id';

	public function __construct($config = array()) {
		$this->setDefaultAdapter(Celsus_Db::getAdapter(Celsus_Db::getDefaultAdapterName()));
		$this->_service = $config['service'];
		parent::__construct($config);
	}

	public function getColumns() {
		return $this->_getCols();
	}

	/**
	 * Returns the table name associated with the model.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Extends the default metadata setup to also add column titles.
	 *
	 * @return boolean
	 */
	protected function _setupMetadata() {
		$isMetadataFromCache = parent::_setupMetadata();
		$defaultFields = $this->getDefaultFields();
		if (null !== $defaultFields) {
			foreach ($this->_metadata as & $col) {
				if (array_key_exists($col['COLUMN_NAME'], $defaultFields)) {
					$col['COLUMN_TITLE'] = $defaultFields[$col['COLUMN_NAME']];
				}
			}
		}
		return $isMetadataFromCache;
	}

	/**
	 * Gets the title of a column.
	 *
	 * @param string $column
	 * @return string
	 */
	public function getColumnTitle(string $column) {
		if (!array_key_exists($column, $this->_metadata)) {
			throw Celsus_Exception("'$column' is not a valid column");
		}
		return array_key_exists('COLUMN_TITLE', $this->_metadata[$column]) ? $this->_metadata[$column]['COLUMN_TITLE'] : null;
	}

	/**
	 * Gets the titles of all this model's columns.
	 *
	 * @return array
	 */
	public function getColumnTitles() {
		foreach ($this->_metadata as $column => $info) {
			$return[$column] = array_key_exists('COLUMN_TITLE', $info) ? $info['COLUMN_TITLE'] : null;
		}
		return $return;
	}

	public function getDefaultFields() {
		return array_keys($this->_getService()->getDefaultFields());
	}

	/**
	 * Sets the title of a column.
	 *
	 * @param string $column
	 * @param string $title
	 */
	public function setColumnTitle(string $column, string $title) {
		if (!array_key_exists($column, $this->_metadata)) {
			throw new Celsus_Exception("'$column' is not a valid column");
		}
		$this->_metadata[$column]['COLUMN_TITLE'] = $title;
	}

	/**
	 * Returns the service associated with this base.
	 *
	 * @return Celsus_Model_Service
	 */
	protected function _getService() {
		return $this->_service;
	}

	/**
	 * Returns the table to use for lookups for the specified column.
	 *
	 * @param string $column
	 * @return string
	 */
	public function getLookupReference($column) {
		if (!isset($this->_lookupReferences[$column])) {
			throw new Celsus_Exception("'$column' is not a referenced column.");
		}
		return $this->_lookupReferences[$column];
	}

	/**
	 * Normalises the lookup references to arrays with the default column name, where not specified.
	 *
	 * @return array
	 */
	public function getNormalisedLookupReferences() {
		$return = array();
		foreach ($this->_lookupReferences as $field => $reference) {
			if (is_array($reference)) {
				$return[$field] = $reference;
			} else {
				$return[$field] = array(
					$reference,
					Celsus_Lookup::DEFAULT_COLUMN
				);
			}
		}
		return $return;
	}

	/**
	 * Gets all the lookup references for this model.
	 *
	 * @return array
	 */
	public function getLookupReferences() {
		return $this->_lookupReferences;
	}

	/**
	 * Gets the table references for this model.
	 *
	 * References can either be a table name, or an array of table name plus referenced field.  This returns
	 * an array of table names as a raw array, keyed on the field that references it ignoring the name
	 * of the field that is referenced in the foreign table.
	 *
	 * @return array
	 */
	public function getLookupReferenceTables() {
		$return = array();
		foreach ($this->_lookupReferences as $field => $reference) {
			$return[$field] = (is_array($reference)) ? $reference[0] : $reference;
		}
		return $return;
	}

	/**
	 * Creates a new row.
	 *
	 * @param array $data
	 * @return Zend_Db_Table_Row
	 */
	public function createRecord(array $data = array()) {
		// Create a Zend_Db_Table_Row.
		return $this->createRow($data);
	}
}
