<?php

/**
 * Used to combine results from multiple deterministic and non-deterministic data sources
 * based on various mixing strategies, with support for diversity filtering, sampling
 * and boosting.
 *
 * Intended to be a general purpose mixer than can be parameterised by its source context.
 *
 * @author majelbstoat
 */
class Celsus_Mixer {

	protected $_operators = array();

	protected $_sources = array();

	protected $_sourceParent = null;

	protected $_sourceTypes = null;

	public function __construct($sourceParent) {
		$interfaces = class_implements($sourceParent);

		if (!in_array('Celsus_Mixer_Source_Interface', $interfaces)) {
			throw new Celsus_Exception("$sourceParent must implement Celsus_Mixer_Source_Interface", Celsus_Http::INTERNAL_SERVER_ERROR);
		}
		$this->_sourceParent = $sourceParent;
	}

	public function setSourceTypes(array $sourceTypes) {
		$sourceParent = $this->_sourceParent;
		$availableSourceTypes = $sourceParent::getTypes();
		$sourceTypeMap = array_flip($availableSourceTypes);

		foreach ($sourceTypes as $sourceType) {
			if (!isset($sourceTypeMap[$sourceType])) {
				$class = get_class($this->_sourceParent);
				throw new Celsus_Exception("$sourceType is not a valid source type for $class", Celsus_Http::INTERNAL_SERVER_ERROR);
			}
		}

		$this->_sourceTypes = $sourceTypes;
	}

	public function getSourceTypes() {
		if (null === $this->_sourceTypes) {
			$sourceParent = $this->_sourceParent;
			$this->_sourceTypes = $sourceParent::getTypes();
		}
		return $this->_sourceTypes;
	}

	public function setSources($sources) {
		foreach ($sources as $source) {
			if (!($source instanceof Celsus_Mixer_Source_Interface)) {
				throw new Celsus_Exception("$source is not a valid source", Celsus_Http::INTERNAL_SERVER_ERROR);
			}
		}

		$this->_sources = $sources;
		return $this;
	}

	public function addSource($source) {
		$this->_sources[] = $source;

		return $this;
	}

	public function clearSources() {
		$this->_sources = array();

		return $this;
	}

	public function getSources() {
		if (!$this->_sources) {
			$sourceTypes = $this->_getSourceTypes();
		}

		// Get the sources here.

		return $this->_sources;
	}

	public function addOperator($operator) {
		$this->_operators[] = $operator;
		return $this;
	}

	public function addOperators(array $operators) {
		$this->_operators = array_merge($this->_operators, $operators);
		return $this;
	}

	public function setOperators(array $operators) {
		$this->_operators = $operators;
		return $this;
	}

	public function mix($count) {

		// First, get the sources that we will be pulling from.
		$sources = $this->getSources();

		$results = array();

		foreach ($sources as $source) {
			$results = array_merge($results, $source->yield($count));
		}

		foreach ($this->_operators as $operator) {
			$results = $operator->process($results);
		}

		return new Celsus_Mixer_Component_Group($results);
	}

	// Source Selection - from the mixing strategy.  All by default (check source parent), or inclusion, or exclusion.

	// Weighing - from the sources themselves.

	//    === all the sources guarantee to return their results in confidence order,
	//        as a bare array with plain incrementing integer keys.


	/**
	 * Quote by jocasa
Hi!

I'm making a computer program that represents some quantities in a graph in this way:

x'i=(xi-xmin)/(xmax-xmin)

so that the possible values of x range from 0 to 1. This is a linear scale. I want to do the same with the logarithmic values of xi. That is, I want to implement a log scale in my graphs, also in the range from 0 to 1.

Can anyone tell me how to do it?

Thanks!
I think this will work:

x'i = (log(xi)-log(xmin)) / (log(xmax)-log(xmin))
As a test, we can see that if xmin,max are 1 and 100,
then xi=10 gives x'i=0.5. As it should, since 10 is halfway between 1 and 100 on a log scale.

----

function logslider(position) {
  // position will be between 0 and 100
  var minp = 0;
  var maxp = 100;

  // The result should be between 100 an 10000000
  var minv = Math.log(100);
  var maxv = Math.log(10000000);

  // calculate adjustment factor
  var scale = (maxv-minv) / (maxp-minp);

  return Math.exp(minv + scale*(position-minp));
}
The resulting values match a logarithmic scale:

js> logslider(0);
100.00000000000004
js> logslider(10);
316.22776601683825
js> logslider(20);
1000.0000000000007
js> logslider(40);
10000.00000000001
js> logslider(60);
100000.0000000002
js> logslider(100);
10000000.000000006
The reverse function would, with the same definitions for minp, maxp, minv, maxv and scale, calculate a slider position from a value like this:

function logposition(value) {
   // set minv, ... like above
   // ...
   return (Math.log(value)-minv) / scale + minp;
}
	 */

}

/**
 * Diversity Strategies
 *
 *
 *
 * DiversityByValue: Application specific.
 */

/**
 * Combination strategies:
 *
 * + Simple: Take all of A, and if not finished take all of B, and if not finished take all of C
 * Decorate Only: Take all the results from source A, replace the source as source B for all items in B that are in A. Repeat for C.
 * + Round Robin: Take one from each in turn until full.
 * Average Confidence: Simple average of confidences for each item.
 * Raw Votes: Each time an item is supplied by a source, increment its count by one.  Probably unbalanced.
 * Summed Confidence: Simply add all the confidences together.
 *
 * MinimumConfidence: Take only those that have the minimum required confidence.
 *
 *
 * Facebook	/ Twitter / LinkedIn				Popular
 * BoostGeneric
 * SummedConfidence
 * MinimumConfidence							MinimumConfidence
 * Diversity By SuperCat
 *                     \
 *                     Decorate =>
 *
 *	// Boosting
	// Deduplicating
	// Ranking
	// Combination
	// Diversity
	// Backfilling
	// Sampling

 *
 * Sources lazily give up their results.
 *
 * array(
 * 	"A" => array(
 * 		"sources" => array("Facebook", "Twitter", "LinkedIn"),
 * 		"operations" => array("BoostGeneric", "SummedConfidence", "MinimumConfidence", "Diversity By SuperCat"),
 * 		"maximum" => 20
 * 	),
 * 	"B" => array(
 * 		"sources" => array("Popular"),
 * 		"operations" => array("MinimumConfidence"),
 * 		"maximum" => 60
 * 	),
 * 	"C" => array(
 * 		"sources" => array("A", "B"),
 * 		"operations" => array("Decorate", "Sampling"),
 * 		"count" => 60,
 * 		"backfill" => array("Popular")
 * 	)
 * )
 *
 * If specify count, must specify backfill.
 *
 *
 *
 *
 *
 *
 */