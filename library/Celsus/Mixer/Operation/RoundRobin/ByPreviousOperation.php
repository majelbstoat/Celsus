<?php

/**
 * The round robin operation takes one result from each source in turn, until the required number is selected.
 *
 * @author majelbstoat
 */
class Celsus_Mixer_Operation_RoundRobin_ByPreviousOperation extends Celsus_Mixer_Operation_RoundRobin_BySource {

	protected $_keyField = 'operations';

	protected $_name = 'roundRobinByPreviousProcess';
}