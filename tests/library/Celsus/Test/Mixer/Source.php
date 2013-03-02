<?php

abstract class Celsus_Test_Mixer_Source extends Celsus_Mixer_Source {

	const SOURCE_TYPE_A = 'A';
	const SOURCE_TYPE_B = 'B';
	const SOURCE_TYPE_C = 'C';

	protected static $_types = array(
		self::SOURCE_TYPE_A,
		self::SOURCE_TYPE_B,
		self::SOURCE_TYPE_C
	);

	protected $_defaultConfig = array(
		'count' => null,
		'exclude' => null
	);

	/**
	 * The results that will be yielded from this dummy source.
	 *
	 * @var Celsus_Mixer_Component_Group $_desiredResults
	 */
	protected $_desiredResults = null;

	/**
	 * Static results that can be set without instantiating an object.
	 *
	 * Useful in testing mixing scenarios where sources aren't explicitly specified.
	 *
	 * @var array $_defaultResults
	 */
	protected static $_defaultResults = array();

	public static function getSource($type, array $config = array()) {
		$classname = get_called_class() . '_' . $type;
		return new $classname($config);
	}

	public function setDesiredResults(Celsus_Mixer_Component_Group $desiredResults) {
		$this->_desiredResults = $desiredResults;
	}

	public function yield(array $config = array()) {
		$this->configure($config);

		$results = $this->_desiredResults ?: Celsus_Test_Mixer_Component::generateSimpleComponentGroup(array(
			$this->_type => static::$_defaultResults
		));

		if ($this->_config['count']) {
			$results = $results->slice($this->_config['count']);
		}

		return $results;
	}

	public static function setDefaultResults($defaultResults) {
		static::$_defaultResults = $defaultResults;
	}

	/**
	 * Helper method to help generate dummy source result sets from simple definitions.
	 *
	 * Takes an array of arrays like:
	 *
	 * $sourceDefinition = array(
	 * 	"A" => array("A", "B"),
	 *	"B" => array("C", "D", "A", "B"),
	 *  "C" => array("G", "H", "I", "J")
	 * );
	 *
	 * @param array $sourceDefinition
	 * @return Celsus_Mixer_Source[]
	 */
	public static function generateSimpleComponentGroupSet($sourceDefinition, $initialConfidence = 100, $confidenceStep = 1) {

		$sources = array();
		foreach ($sourceDefinition as $sourceType => $desiredResults) {
			$confidence = $initialConfidence;
			$results = array();
			$source = self::getSource($sourceType);
			foreach ($desiredResults as $desiredResult) {
				$results[] = new Celsus_Mixer_Component(array(
					'confidence' => $confidence,
					'label' => $desiredResult,
					'result' => null,
					'source' => $sourceType
				));
				$confidence -= $confidenceStep;
			}
			$source->setDesiredResults(new Celsus_Mixer_Component_Group($results));
			$sources[] = $source;
		}

		return $sources;
	}
}