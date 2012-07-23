<?php

class Celsus_View_Helper_Head extends Celsus_View_Helper_Tag {

	/**
	 * @var Celsus_OpenGraph_Object
	 */
	protected $_openGraphObject = null;

	protected $_charset = 'UTF-8';

	protected $_script = null;

	protected $_scriptConfig = array();

	public function setOpenGraphObject($object) {
		$this->_openGraphObject = $object;
		return $this;
	}

	public function setIcon($url) {
		return $this->_addTag('link', array(
			'rel' => 'shortcut icon',
			'href' => $url
		));
	}

	public function setCharset($charset) {
		$this->_charset = $charset;
		return $this;
	}

	public function setViewport($width = 'device-width', $initialScale = 1, $maximumScale = 1) {
		return $this->_addTag('meta', array(
			'name' => 'viewport',
			'content' => "width=$width, initial-scale=$initialScale, maximum-scale=$maximumScale"
		));
	}

	public function setDescription($description) {
		return $this->_addTag('meta', array(
			'name' => 'description',
			'content' => $description
		));
	}

	public function setScript($script, array $config = array()) {
		$this->_script = $script;
		$this->_scriptConfig = $config;
		return $this;
	}

	public function render() {
		$headAttributes = $this->_getHeadAttributes();
		?>
<head<?= $headAttributes ?>>
	<meta charset="<?= $this->_charset ?>">
	<?= Celsus_View_Helper_Broker::getHelper('pageTitle') ?>

<?= Celsus_View_Helper_Broker::getHelper('pageStyle') ?>
<?= $this->_script() ?>
<?= $this->_tags(); ?>
</head>
<?php
	}

	protected function _script() {
		if ($this->_script) {
			?>
	<script type="text/javascript">Config = { "<?= $this->_script ?>": <?= Zend_Json::encode($this->_scriptConfig) ?> };</script>
	<script type="text/javascript" id="loader" data-main="<?= $this->_script ?>" src="/js/celsus/loader.js"></script>
			<?php
		}
	}

	protected function _getHeadAttributes() {
		$attributes = array();
		if ($this->_openGraphObject) {
			$prefixes = Celsus_OpenGraph::getNamespaces($this->_openGraphObject->getPrefixes());
			$namespaces = array();
			foreach ($prefixes as $prefix => $url) {
				$namespaces[] = "$prefix: $url";
			}
			$attributes[] = 'prefix="' . rtrim(implode("# ", $namespaces)) . '"';
		}
		return $attributes ? ' ' . implode(" ", $attributes) : '';
	}

	protected function _tags() {
		if ($this->_openGraphObject) {
			// @todo Add open graph tags.
		}
		return parent::_tags();
	}

}