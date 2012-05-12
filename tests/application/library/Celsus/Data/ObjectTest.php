<?php

class Celsus_Data_ObjectTest extends PHPUnit_Framework_TestCase {

	protected $_sampleData = null;

	protected $_object = null;

	public function setUp() {
		$this->_sampleData = array(
			'name' => 'Jamie Talbot',
			'dob' => '1981-06-02',
			'height' => 185,
			'weight' => 83
		);
		$this->_object = new Celsus_Data_Object($this->_sampleData);
	}

	public function testCanConstructWithAnArray() {
		$data = $this->_object->toArray();
		$this->assertEquals($this->_sampleData, $data);
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testCannotConstructWithANonArrayableObject() {
		$nonArrayableObject = new Celsus_Test_NonArrayable();
		$object = new Celsus_Data_Object($nonArrayableObject, "Ignored");
	}

	/**
	 * Test that a minimal class that provides at least toArray() will be allowed.
	 *
	 * @uses toArray() Mocked version.
	 *
	 * Self-shunted.  This test class provides toArray().
	 */
	public function testCanConstructWithAnArrayableObject() {
		$object = new Celsus_Data_Object($this, "Dummy");
		$this->assertTrue(true);
	}

	public function testDefaultFilterShouldReturnAllData() {
		$nonFilteredData = $this->_object->toArray();
		$this->assertEquals($this->_sampleData, $nonFilteredData);
	}

	public function testDeesFilterShouldReturnDateOfBirth() {
		$this->_object->setFilter('Celsus_Data_Filter_DeesOnly');
		$filteredData = $this->_object->toArray();
		$expected = array('dob' => '1981-06-02');
		$this->assertEquals($expected, $filteredData);
	}

	public function testCanOutputToJson() {
		$json = $this->_object->toJson();
		$this->assertEquals($this->_sampleData, Zend_Json::decode($json));
	}

	/**
	 * @expectedException Celsus_Exception
	 */
	public function testInvalidFormatterShouldCauseError() {
		// This should fail as it doesn't implement the correct interface.
		$this->_object->setFilter('Celsus_Data_Object');
	}

	public function testCanOutputToCsv() {
		$csv = $this->_object->toCSV();

		// Yes, this is correct.  And yes, this kind of thing is messy to test :)
		$expected = "name,dob,height,weight\n" . '"Jamie Talbot",1981-06-02,185,83' . "\n";
		$this->assertEquals($expected, $csv);
	}

	public function testCanGetFieldThroughDirectAccess() {
		$this->assertEquals("Jamie Talbot", $this->_object->name);
	}

	public function testReadingFieldShouldBeDeniedThroughDirectAccessIfNotAllowed() {
		$this->_object->setFilter('Celsus_Data_Filter_DeesOnly');
		$this->assertNull($this->_object->name);
		$this->assertEquals("1981-06-02", $this->_object->dob);
	}

	// Self Shunting Code.

	/**
	 * Mocks the ability to output in array form.
	 */
	public function toArray() {
		return array(
			"name" => "dummy"
			);
	}
}
