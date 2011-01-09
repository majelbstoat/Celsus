<?php

class Celsus_Validate_Password extends Zend_Validate_Abstract {

    const INVALID      = 'alnumInvalid';
    const NOT_PASSWORD = 'notPassword';
    const STRING_EMPTY = 'stringEmpty';

    /**
     * The model to check for uniqueness in.
     *
     * @var Celsus_Model
     */
    protected $_model;

    /**
     * An identifier of the row with which this value clashes, if applicable
     *
     * @var string

    protected $_clash;*/

    /**
     * The name of the model which contains the clash, if applicable
     *
     * @var string

    protected $_modelName;  */


    /**
     * The model field that is human readable for return messages.
     *
     * @var string
     */
    protected $_identifier = 'name';

    /**
     * Alphanumeric filter used for validation
     *
     * @var Zend_Filter_Alnum
     */
    protected static $_filter = null;
    /**
     * Variable substitution map.
     *
     * @var array

    protected $_messageVariables = array(
        'clash' => '_clash',
        'model' => '_modelName'
    );*/

    /**
     * The field to test for uniqueness on.
     *
     * @var string

    protected $_field;
*/
    /**
     * @var array
     */
   
    protected $_messageTemplates = array(
        self::INVALID      => "This field must be valid password. A valid password is in the form 12YUabs!123",
        self::NOT_PASSWORD    => "'%value%' has not only alphabetic and digit characters",
        self::STRING_EMPTY => "'%value%' is an empty string"
    );

    /**
     * Sets default option values for this instance
     *
     * @param  boolean $allowWhiteSpace
     * @return void
     */
    public function __construct($allowWhiteSpace = false)
    {
        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
    }

    /**
     * Sets the model to validate against.
     *
     * @param Celsus_Model $model

    public function setModel(Celsus_Model $model) {
        $this->_model = $model;
    } */

    /**
     * Sets the identifier for generating a clash message, if necessary.
     *
     * @param string

    public function setIdentifier($identifier) {
        $this->_identifier = $identifier;
    }*/

    /**
     * Sets the field for determining uniqueness.
     *
     * @param string

    public function setField($field) {
        $this->_field = $field;
    }*/

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is unique in the specified table.
     *
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value contains only alphabetic and digit characters
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);

        if ('' === $value) {
            $this->_error(self::STRING_EMPTY);
            return false;
        }

        if (null === self::$_filter) {
            /**
             * @see Zend_Filter_Alnum
             */
            require_once 'Zend/Filter/Alnum.php';
            self::$_filter = new Zend_Filter_Alnum();
        }

        self::$_filter->allowWhiteSpace = false;

        if ($value != self::$_filter->filter($value)) {
            $this->_error(self::NOT_ALNUM);
            return false;
        }

        return true;
    }

    /**
     * Returns code snippet used for client-side validation.
     *
     * @param string $name The name of the element.
     * @return boolean No client-side validation for unique constraint.
     */
    public function getClientSideValidation($name) {
        return false;
    }

}
