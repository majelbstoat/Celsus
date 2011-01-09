<?php

/**
 * Abstract class representing a grid layout used to display and filter information.
 */
abstract class Celsus_Grid implements Countable {
	
	const DEFAULT_PAGE_LIMIT = 30;
	
	/**
	 * The columns to display in the grid.
	 * 
	 * @var array
	 */
	protected $_columns = array();
	
	/**
	 * Decorators used to render this grid.
	 *
	 * @var array
	 */
	protected $_decorators = null;

	/**
	 * The optional model that describes this data.
	 *
	 * @var Celsus_Model
	 */
	protected $_model = null;

	/**
	 * The number of rows to display per page.
	 * 
	 * @var int
	 */
	protected $_pageLimit = self::DEFAULT_PAGE_LIMIT;
	
	/**
	 * The class representing a single row of the grid.
	 *
	 * @var Celsus_Grid_Row
	 */
	protected $_rowClass = null;

	/**
	 * The rowset object holding the data to lay out.
	 *
	 * @var Celsus_Model_Rowset
	 */
	protected $_rowSet = null;

	/**
	 * Creates a new grid.
	 *
	 * @param Celsus_Model_Rowset $rowSet
	 * @param array $config
	 */
	public function __construct($options = null) {
		$class = get_class($this);
		$this->_rowClass = $class . '_Row';

		// Handle options
		if (is_array($options)) {
			$this->setOptions($options);
		}

		$this->init();

		$this->loadDefaultDecorators();
	}

	/**
	 * Additional operations to perform on grid creation.
	 */
	public function init() {}

	// Decorator functions

	/**
	 * Loads default decorators if none have been defined already.
	 */
	public function loadDefaultDecorators() {
		$decorators = $this->getDecorators();
		if (empty($decorators)) {
//			$this->addDecorator('FormElements')
//			->addDecorator('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form'))
//			->addDecorator('Form');
		}
	}

	/**
	 * Returns currently loaded decorators.
	 *
	 * @return array
	 */
	public function getDecorators() {
		return $this->_decorators;
	}

	/**
	 * Add a decorator for rendering the grid
	 *
	 * @param  string|Zend_Form_Decorator_Interface $decorator
	 * @param  array|Zend_Config $options Options with which to initialize decorator
	 * @return Celsus_Grid
	 */
	public function addDecorator($decorator, $options = null) {
		if ($decorator instanceof Zend_Form_Decorator_Interface) {
			$name = get_class($decorator);
		} elseif (is_string($decorator)) {
			$decorator = $this->_getDecorator($decorator, $options);
			$name = get_class($decorator);
		} elseif (is_array($decorator)) {
			foreach ($decorator as $name => $spec) {
				break;
			}
			if (is_numeric($name)) {
				require_once 'Zend/Form/Exception.php';
				throw new Zend_Form_Exception('Invalid alias provided to addDecorator; must be alphanumeric string');
			}
			if (is_string($spec)) {
				$decorator = $this->_getDecorator($spec, $options);
			} elseif ($spec instanceof Zend_Form_Decorator_Interface) {
				$decorator = $spec;
			}
		} else {
			require_once 'Zend/Form/Exception.php';
			throw new Zend_Form_Exception('Invalid decorator provided to addDecorator; must be string or Zend_Form_Decorator_Interface');
		}

		$this->_decorators[$name] = $decorator;

		return $this;
	}

	/**
	 * Renders the grid.  Data in the rowSet is assumed to be in the correct order.
	 *
	 * @return string
	 */
	public function render() {
		var_dump(count($this->_rowSet) . " rows");
		return '';
	}

	// Countable Implementation

	/**
	 * Counts the number of rows in the rowset.
	 */
	public function count() {
		return count($this->_rowSet);
	}

	// Magic functions
	
	/**
	 * Syntactic sugar to allow for direct echoing of the grid.  Proxies to {@link render()}.
	 *
	 * @return string
	 */
	public function __toString() {
		$grid = $this->render();
		return $grid;
	}

	// Getters and Setters
	
	/**
	 * Gets the columns for this grid.
	 * 
	 * @return array
	 */
	public function getColumns() {
		return $this->_columns;
	}

	/**
	 * Sets grid options.
	 *
	 * @param array $options
	 */
	public function setOptions(array $options) {
		if (isset($options['model'])) {
			$this->_model = $options['model'];
			unset($options['model']);
		}
	}

	/**
	 * Returns the rowset object associated with the grid.
	 *
	 * @return Celsus_Model_Rowset
	 */
	public function getRowset() {
		return $this->_rowSet;
	}

	/**
	 * Sets the grid's rowset object.
	 *
	 * @param Celsus_Model_Rowset
	 * @return Celsus_Grid
	 */
	public function setRowSet($rowSet) {
		$this->_rowSet = $rowSet;
		return $this;
	}
	
	/**
	 * Gets the number of rows to display per page.
	 * 
	 * @return int
	 */
	public function getPageLimit() {
		return $this->_pageLimit;
	}

	/**
	 * Gets the number of rows to display per page.
	 * 
	 * @param int
	 * @throws Celsus_Exception
	 */
	public function setPageLimit($pageLimit) {
		if (!is_int($pageLimit)) {
			throw new Celsus_Exception("Page Limit must be an integer!");
		}
		$this->_pageLimit = $pageLimit;
	}
}

?>