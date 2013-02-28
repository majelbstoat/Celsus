<?php

interface Celsus_Mixer_Operation_Interface {

	/**
	 * @param Celsus_Mixer_Component_Group $results
	 * @return Celsus_Mixer_Component_Group
	 */
	public function process(Celsus_Mixer_Component_Group $results);

}