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

	protected $_sourceParent;

	public function __construct(Celsus_Mixer_Source_Interface $sourceParent) {
		$this->_sourceParent = $sourceParent;
	}

	public function get($count) {

	}

	// Source Selection - from the mixing strategy.  All by default (check source parent), or inclusion, or exclusion.

	// Weighing - from the sources themselves.

	//    === all the sources guarantee to return their results in confidence order,
	//        as a bare array with plain incrementing integer keys.

	// Boosting - according to mixing strategy.  Potentially assign a boosting multiplier for each strategy.

	// Deduplicating - this mixer.

	// Ranking - this mixer

	// Combination - according to mixing strategy

	// Diversity - according to diversity strategy  ?? Difficult

	// Backfilling - from designated backfill source.

	// Sampling - from designated sample source

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
 * Combination strategies:
 *
 * Simple: Take all of A, and if not finished take all of B, and if not finished take all of C
 * Decorate Only: Take all the results from source A, replace the source as source B for all items in B that are in A. Repeat for C.
 * Round Robin: Take one from each in turn until full.
 * Average Confidence: Simple average of confidences for each item.
 * Raw Votes: Each time an item is supplied by a source, increment its count by one.  Probably unbalanced.
 * Summed Confidence: Simply add all the confidences together.
 */