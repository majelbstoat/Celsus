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

	public function setAuthenticationRequirement($shouldAuthenticate) {
		$this->_shouldAuthenticate = $shouldAuthenticate;
	}

	public function authenticate(Celsus_State $state) {

		$route = $state->getRoute();
		if (!$this->_shouldAuthenticate || !$route->requiresAuthentication() || $state->hasException()) {
			return;
		}

		$auth = Celsus_Auth::getInstance();

		// @todo Test the
		if (!$auth->hasIdentity()) {
			// We don't have an identity and one is needed, so send the user to the login route.
			throw new Celsus_Exception("This route requires authorisation", Celsus_Http::UNAUTHORISED);
		}
	}

}