<?php

class Celsus_Db_Document_Adapter_Facebook {

	const DATA_TYPE_PROFILE_INFO = '';

	/**
	 * @var Zend_Http_Client HTTP client used for accessing server
	 */
	protected $_client;

	protected $_config = array(
		'host' => 'graph.facebook.com',
		'ssl' => true,
	);

	// The base URI for this connection.
	protected $_baseUri = null;

	// Whether the connection information has changed.
	protected $_dirty = true;

	public function __construct($config = array()) {
		if (null !== $config) {
			if (is_array($config)) {
				$this->setFromArray($config);
			} elseif ($config instanceof Zend_Config) {
				$this->setFromConfig($config);
			}
		}
	}

	public function setFromArray(array $options) {
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	public function setFromConfig(Zend_Config $config) {
		return $this->setFromArray($config->toArray());
	}

	public function setApplicationId($applicationId) {
		$this->_config['applicationId'] = $applicationId;
		return $this;
	}

	public function setApplicationSecret($applicationSecret) {
		$this->_config['applicationSecret'] = $applicationSecret;
		return $this;
	}

	public function setApplicationNamespace($applicationNamespace) {
		$this->_config['applicationNamespace'] = $applicationNamespace;
		return $this;
	}

	/**
	 * Retrieves documents from facebook based on access tokens, or null if nothing is found.
	 *
	 * @param string|array $id
	 * @return Celsus_Db_Document_Set_Facebook
	 */
	public function find($accessTokens) {
		return $this->getUserData($accessTokens);
	}

	/**
	 * Retrieves documents from facebook based on access tokens, or null if nothing is found.
	 *
	 * @param string|array $id
	 * @param string $dataType
	 * @throws Celsus_Exception
	 * @return Celsus_Db_Document_Set_Facebook
	 */
	public function getUserData($accessTokens, $dataType = self::DATA_TYPE_PROFILE_INFO) {
		if (!is_array($accessTokens)) {
			$accessTokens = array($accessTokens);
		}

		foreach ($accessTokens as $accessToken) {
			$parameters = array(
				'access_token' => $accessToken
			);
			$response = $this->_prepare("/me/$dataType", $parameters)->_execute(Zend_Http_Client::GET);

			$status = $response->getStatus();
			$return = null;
			switch ($status) {
				case Celsus_Http::OK:
					$return = new Celsus_Db_Document_Set_Facebook(array(
						'adapter' => $this
					));
					$document = new Celsus_Db_Document_Facebook(array(
						'adapter' => $this,
						'data' => Zend_Json::decode($response->getBody())
					));
					$return->add($document);
					break;

				case Celsus_Http::NOT_FOUND:
					break;

				case Celsus_Http::UNAUTHORIZED:
					throw new Celsus_Exception("Access token was invalid");
					break;

				case Celsus_Http::BAD_REQUEST:
					throw new Celsus_Exception("Error in call: " . $response->getBody());
					break;

				default:
					throw new Celsus_Exception("Response code $status not handled.");
					break;
			}
		}
		return $return;
	}

	public function acquireAccessToken($authorisationCode, $callbackUrl) {
		$parameters = array(
			'client_id' => $this->_config['applicationId'],
			'client_secret' => $this->_config['applicationSecret'],
			'redirect_uri' => $callbackUrl,
			'grant_type' => 'authorization_code',
			'code' => $authorisationCode
		);

		$response = $this->_prepare("/oauth/access_token", $parameters)->_execute(Zend_Http_Client::GET);

		$status = $response->getStatus();
		$return = array();
		switch ($status) {
			case Celsus_Http::OK:
				parse_str($response->getBody(), $responseData);
				return $responseData['access_token'];
				break;

			case Celsus_Http::BAD_REQUEST:
				throw new Celsus_Exception("Error in call: " . $response->getBody());
				break;

			default:
				throw new Celsus_Exception("Response code $status not handled.");
				break;
		}
	}

	/**
	 * Saves a document, either by creating or updating it.
	 *
	 * @param Celsus_Db_Document_Couch $document
	 * @param string $method
	 * @throws Celsus_Exception
	 */
	public function save(Celsus_Db_Document_Couch $document, $method = Zend_Http_Client::PUT) {
		throw new Celsus_Exception("Not supported yet.");
	}

	public function getProtocol() {
		return $this->_config['ssl'] ? "https://" : "http://";
	}

	protected function _getBaseUri() {
		if (!$this->_dirty) {
			return $this->_baseUri;
		}

		// Save the constructed base URI and mark it clean so we don't regenerate next time.
		$this->_baseUri = $this->getProtocol() . $this->getHost();
		$this->_dirty = false;

		return $this->_baseUri;
	}

	public function getHost() {
		return $this->_config['host'];
	}

	public function ping() {
		return $this->_prepare('/')->_execute()->getBody();
	}

	public function _prepare($path, $parameters = null) {
		$client = $this->getHttpClient();
		$base = $this->_getBaseUri();
		$path = ltrim($path, '/');
		$client->setUri("$base/$path");
		if (null !== $parameters) {
			$client->setParameterGet($parameters);
		}
		return $this;
	}

	protected function _execute($method = Zend_Http_Client::GET) {
		$client = $this->getHttpClient();
		$response = $client->request($method);
		$client->resetParameters();
		return $response;
	}

	/**
	 * Get current HTTP client
	 *
	 * @return Zend_Http_Client
	 */
	public function getHttpClient() {
		if (null === $this->_client) {
			$this->_client = new Zend_Http_Client();
		}
		return $this->_client;
	}
}
