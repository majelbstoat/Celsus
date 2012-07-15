<?php

class Celsus_Service_AuthenticationManager {

	protected $_shouldAuthenticate = false;

	/**
	 * Uses a number of strategies to try and authenticate a client.
	 * @param Celsus_State $state
	 */
	public function determineIdentity(Celsus_State $state) {

		// Try: cookies, Facebook, oauthtoken etc

		// @todo Make this iterate through a number of different authentication mechanisms.


		// Set the identity on the State.
	}

	public function authenticate(Celsus_State $state) {

		$route = $state->getRoute();
		if (!$this->shouldAuthenticate() || !$route->requiresAuthentication() || $state->hasException()) {
			return;
		}

		// @todo Test the
		if (!$state->hasIdentity()) {
			// We don't have an identity and one is needed, so send the user to the login route.
			throw new Celsus_Exception("This route requires authorisation", Celsus_Http::UNAUTHORISED);
		}
	}

	/**
	 * Determines whether authentication is required.
	 *
	 * If a parameter is supplied, sets the flag.  If not, reads it.
	 *
	 * @param boolean $shouldAuthenticate
	 * @return boolean|Celsus_Service_AuthenticationManager
	 */
	public function shouldAuthenticate($shouldAuthenticate = null) {
		if (null === $shouldAuthenticate) {
			return $this->_shouldAuthenticate;
		} else {
			$this->_shouldAuthenticate = $shouldAuthenticate;
			return $this;
		}
	}
}