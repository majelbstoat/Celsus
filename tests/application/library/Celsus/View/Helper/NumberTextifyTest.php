<?php

class Celsus_View_Helper_NumberTextifyTest extends PHPUnit_Framework_TestCase {

	/**
	 * The helper to use for testing.
	 *
	 * @var Celsus_View_Helper_NumberTextify
	 */
	protected $_helper = null;

	public function setUp() {
		$this->_helper = new Celsus_View_Helper_NumberTextify();
	}

	public function testEleven() {
		$output = "Eleven";
		$this->assertEquals($output, $this->_helper->numberTextify(11));
	}

	public function testFiftyEight() {
		$output = "Fifty Eight";
		$this->assertEquals($output, $this->_helper->numberTextify(58));
	}

	public function testOneHundredAndFifteen() {
		$output = "One Hundred And Fifteen";
		$this->assertEquals($output, $this->_helper->numberTextify(115));
	}

	public function testOneHundredAndFiftyEight() {
		$output = "One Hundred And Fifty Eight";
		$this->assertEquals($output, $this->_helper->numberTextify(158));
	}

	public function testFiveHundredAndEight() {
		$output = "Five Hundred And Eight";
		$this->assertEquals($output, $this->_helper->numberTextify(508));
	}

	public function testTenThousandAndFiftyEight() {
		$output = "Ten Thousand And Fifty Eight";
		$this->assertEquals($output, $this->_helper->numberTextify(10058));
	}

	public function testFourThousandAndFiftyEight() {
		$output = "Four Thousand And Fifty Eight";
		$this->assertEquals($output, $this->_helper->numberTextify(4058));
	}

	public function testOneMillionElevenThousandAndFiftyEight() {
		$output = "One Million Eleven Thousand And Fifty Eight";
		$this->assertEquals($output, $this->_helper->numberTextify(1011058));
	}

	public function testSeventeenThousandAndTwelve() {
		$output = "Seventeen Thousand And Twelve";
		$this->assertEquals($output, $this->_helper->numberTextify(17012));
	}

	public function testFourHundredAndFortyOneThousand() {
		$output = "Four Hundred And Forty One Thousand";
		$this->assertEquals($output, $this->_helper->numberTextify(441000));
	}

	public function testTwoHundredAndElevenThousandAndThirteen() {
		$output = "Two Hundred And Eleven Thousand And Thirteen";
		$this->assertEquals($output, $this->_helper->numberTextify(211013));
	}


	public function testOneHundredThousandAndFiftyEight() {
		$output = "One Hundred Thousand And Fifty Eight";
		$this->assertEquals($output, $this->_helper->numberTextify(100058));
	}

	public function testOneMillionTwoHundredThousandAndFiftyEight() {
		$output = "One Million Two Hundred Thousand And Fifty Eight";
		$this->assertEquals($output, $this->_helper->numberTextify(1200058));
	}

	public function testFiveMillionAndEightyFive() {
		$output = "Five Million And Eighty Five";
		$this->assertEquals($output, $this->_helper->numberTextify(5000085));
	}
}

?>