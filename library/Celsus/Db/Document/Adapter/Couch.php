<?php

class Celsus_Db_Document_Adapter_Couch {

	/**
	 * @var Zend_Http_Client HTTP client used for accessing server
	 */
	protected $_client;

	protected $_config = array(
		'db' => null,
		'host' => '127.0.0.1',
		'port' => 5984,
		'ssl' => false,
		'username' => null,
		'password' => null
	);

	// The base URI for this connection.
	protected $_baseUri = null;

	// Whether the connection information has changed.
	protected $_dirty = false;

	public function __construct($config = array()) {
		if (null !== $config) {
			if (is_array($config)) {
				$this->setFromArray($config);
			} elseif ($config instanceof Zend_Config) {
				$this->setFromConfig($config);
			} elseif (is_string($config)) {
				$this->setDb($config);
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

	/**
	 * Set Database on which to perform operations
	 *
	 * @param  string $db
	 * @return Celsus_Db_Document_Adapter_Couch
	 */
	public function setDb($db) {
		if (!preg_match('/^[a-z][a-z0-9_$()+-\/]+$/', $db)) {
			throw new Celsus_Exception(sprintf('Invalid database specified: "%s"', htmlentities($db)));
		}
		$this->_config['db'] = $db;
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Retrieve current database name
	 *
	 * @return string|null
	 */
	public function getDb() {
		return $this->_config['db'];
	}

	/**
	 * Sets whether to use SSL.
	 *
	 * @param bool $ssl
	 */
	public function setSsl($ssl) {
		$this->_config['ssl'] = !!$ssl;
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Retrieve SSL settings.
	 *
	 * @return bool
	 */
	public function getSsl() {
		return $this->_config['ssl'];
	}

	/**
	 * Sets username to use.
	 *
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->_config['username'] = $username;
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Retrieve username.
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->_config['username'];
	}

	/**
	 * Sets whether to use a password.
	 *
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->_config['password'] = $password;
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Retrieve password.
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->_config['password'];
	}

	/**
	 * Set database host
	 *
	 * @param  string $host
	 * @return Celsus_Db_Document_Adapter_Couch
	 */
	public function setHost($host) {
		$this->_config['host'] = $host;
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Retrieve database host
	 *
	 * @return string
	 */
	public function getHost() {
		return $this->_config['host'];
	}

	/**
	 * Set database host port
	 *
	 * @param  int $port
	 * @return Celsus_Db_Document_Adapter_Couch
	 */
	public function setPort($port) {
		$this->_config['port'] = (int) $port;
		$this->_dirty = true;
		return $this;
	}

	/**
	 * Retrieve database host port
	 *
	 * @return int
	 */
	public function getPort() {
		return $this->_config['port'];
	}

	/**
	 * Queries a view.
	 *
	 * @param Celsus_Db_Document_View
	 * @return Celsus_Db_Document_CouchSet
	 */
	public function view(Celsus_Db_Document_View $view) {
		$db = $this->getDb();
		$designDocument = $view->getDesignDocument();
		$name = $view->getName();
		$parameters = $view->getParameters();
		$response = $this->_prepare("/$db/_design/$designDocument/_view/$name", $parameters)->_execute();

		$status = $response->getStatus();
		switch ($status) {
			case Celsus_Http::OK:
				return new Celsus_Db_Document_CouchSet(array(
					'adapter' => $this,
					'data' => $response->getBody()
				));
				break;

			case Celsus_Http::NOT_FOUND:
				throw new Celsus_Exception("$name is not a valid view in the $designDocument document.");
				break;

			case Celsus_Http::BAD_REQUEST:
				// Usually a JSON formatting error.
				throw new Celsus_Exception("Request was poorly formatted");
				break;

			default:
				throw new Celsus_Exception("Response code $status not handled.");
				break;
		}
	}

	/**
	 * Retrieves documents from the database based on IDs, or null if nothing is found.
	 *
	 * @param string|array $id
	 * @throws Celsus_Exception
	 * @return Celsus_Db_Document_CouchSet
	 */
	public function find($identifiers) {
		$db = $this->getDb();
		if (!is_array($identifiers)) {
			$identifiers = array($identifiers);
		}
		$postData = new stdClass();
		$postData->keys = $identifiers;
		$parameters = array(
			'include_docs' => true
		);

		$this->getHttpClient()->setRawData(Zend_Json::encode($postData));
		$response = $this->_prepare("/$db/_all_docs", $parameters)->_execute(Zend_Http_Client::POST);

		$status = $response->getStatus();
		switch ($status) {
			case Celsus_Http::OK:
				return new Celsus_Db_Document_CouchSet(array(
					'adapter' => $this,
					'data' => $response->getBody()
				));
				break;

			case Celsus_Http::NOT_FOUND:
				return null;
				break;

			case Celsus_Http::UNAUTHORISED:
				throw new Celsus_Exception("Database username and password were incorrect");
				break;

			case Celsus_Http::BAD_REQUEST:
				throw new Celsus_Exception("Error in call: " . $response->getBody());
				break;

			case Celsus_Http::UNSUPPORTED_MEDIA_TYPE:
				throw new Celsus_Exception("Content type must be application/json");
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
		$db = $this->getDb();
		$id = $document->getId();

		// Set the raw data to be saved.
		$this->getHttpClient()->setRawData($document->toJson());
		$response = $this->_prepare("/$db/$id")->_execute($method);

		$status = $response->getStatus();
		switch ($status) {
			case Celsus_Http::OK:
			case Celsus_Http::CREATED:

				$decodedResponse = Zend_Json::decode($response->getBody());
				$revision = $decodedResponse['rev'];
				break;

			case Celsus_Http::PRECONDITION_FAILED:
				throw new Celsus_Exception("A document with the ID $id already exists");
				break;

			case Celsus_Http::CONFLICT:
				throw new Celsus_Exception("There was a conflict updating the database");
				break;

			case Celsus_Http::UNAUTHORISED:
				throw new Celsus_Exception("Database username and password were incorrect");
				break;

			case Celsus_Http::BAD_REQUEST:
				throw new Celsus_Exception("Error in call: " . $response->getBody());
				break;

			case Celsus_Http::UNSUPPORTED_MEDIA_TYPE:
				throw new Celsus_Exception("Content type must be application/json");
				break;

			default:
				throw new Celsus_Exception("Response code $status not handled." . $response->getBody());
				break;
		}
		return $revision;
	}

	protected function _getBaseUri() {
		if (!$this->_dirty) {
			return $this->_baseUri;
		}
		$portNumber = $this->getPort();
		if ($this->_config['ssl']) {
			$s = 's';
			$port = (443 != $portNumber) ? ":$portNumber" : '';
		} else {
			$s = '';
			$port = (80 != $portNumber) ? ":$portNumber" : '';
		}

		$username = $this->getUsername();
		if ($username) {
			$password = $this->getPassword();
			if (!$password) {
				throw new Celsus_Exception("Must specify both a username and password for authentication.");
			}
			$credentials = "$username:$password@";
		} else {
			$credentials = '';
		}

		// Save the constructed base URI and mark it clean so we don't regenerate next time.
		$this->_baseUri = "http$s://" . $credentials . $this->getHost() . $port;
		$this->_dirty = false;

		return $this->_baseUri;
	}

	public function ping() {
		return $this->_prepare('/')->_execute()->getBody();
	}

	public function _prepare($path, $parameters = null) {
		$client = $this->getHttpClient();
		$client->setHeaders('Content-Type', 'application/json');
		$base = $this->_getBaseUri();
		$path = ltrim($path, '/');
		$client->setUri("$base/$path");
		if (null !== $parameters) {
			foreach ($parameters as $key => $value) {
				$parameters[$key] = Zend_Json::encode($value);
			}
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
