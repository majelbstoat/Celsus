<?php

class Celsus_Url_CanonicaliserTest extends PHPUnit_Framework_TestCase {

	// Tests
	public function provideUrls() {
		return array(
			'youtube' => array(
				'http://www.youtube.com/watch?v=prjhQcqiGQc&search_query=ant+death+spiral',
				'http://www.youtube.com/watch?v=prjhQcqiGQc'
			)
		);

	}

	/**
	 * @param array $data
	 * @dataProvider provideUrls
	 */
	public function testShouldBeAbleToCanonicaliseUrls($url, $canonicalised) {

		$results = Celsus_Url_Canonicaliser::canonicalise($url);

		$this->assertEquals($canonicalised, $results->first()->canonicalised());
	}
}