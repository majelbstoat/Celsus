<?php

class Celsus_Service_AuthenticationManager {

	protected $_shouldTestAuthorisation = false;

	/**
	 * Uses a number of strategies to try and authenticate a client.
	 * @param Celsus_State $state
	 */
	public function authenticate(Celsus_State $state) {

		// Try: cookies, Facebook, oauthtoken etc

		// @todo Make this iterate through a number of different authentication mechanisms.


		// Set the identity on the State.
	}

	public function testAuthorisation(Celsus_State $state) {

		// First of all, determine the user's identity.
		$this->authenticate($state);

		$route = $state->getRoute();
		if (!$this->shouldTestAuthorisation() || !$route->requiresAuthorisation() || $state->hasException()) {
			return;
		}

		// @todo Also test API authentication.

		if (!$state->hasIdentity()) {
			// We don't have an identity and one is needed.
			$state->authorised(false);
		}
	}

	/**
	 * Determines whether we should test authorisation.
	 *
	 * If a parameter is supplied, sets the flag.  If not, reads it.
	 *
	 * @param boolean $shouldTestAuthorisation
	 * @return boolean|Celsus_Service_AuthenticationManager
	 */
	public function shouldTestAuthorisation($shouldTestAuthorisation = null) {
		if (null === $shouldTestAuthorisation) {
			return $this->_shouldTestAuthorisation;
		} else {
			$this->_shouldTestAuthorisation = $shouldTestAuthorisation;
			return $this;
		}
	}
}