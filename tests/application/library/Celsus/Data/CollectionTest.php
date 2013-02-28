<?php

class Celsus_Data_CollectionTest extends PHPUnit_Framework_TestCase {

	// Tests

	public function testSlicingACollectionShouldReturnAnObjectOfTheSameType() {
		$componentGroup = new Celsus_Mixer_Component_Group();

		$sliced = $componentGroup->slice(5);

		$this->assertSame(get_class($componentGroup), get_class($sliced), "Slicing should not change the collection type.");
	}
}