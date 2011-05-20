<?php
/**
 * Celsus
 *
 * @category Celsus
 * @copyright Copyright (c) 2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: Inflector.php 72 2010-09-14 01:56:33Z jamie $
 */

/**
 * Inflection functionality
 *
 * @defgroup Celsus_Inflection Celsus Inflection
 */

/**
 * Provides useful functionality for the pluralisation of English nouns.
 *
 * @ingroup Celsus_Inflection
 */
class Celsus_Inflector {

	/**
	 * Plural inflector rules
	 *
	 * @var array
	 * @access protected
	 **/
	private static $_plural = array(
		'rules' => array(
			'/(s)tatus$/i' => '\1\2tatuses',
			'/(quiz)$/i' => '\1zes',
			'/^(ox)$/i' => '\1\2en',
			'/([m|l])ouse$/i' => '\1ice',
			'/(matr|vert|ind)(ix|ex)$/i'  => '\1ices',
			'/(x|ch|ss|sh)$/i' => '\1es',
			'/([^aeiouy]|qu)y$/i' => '\1ies',
			'/(hive)$/i' => '\1s',
			'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
			'/sis$/i' => 'ses',
			'/([ti])um$/i' => '\1a',
			'/(p)erson$/i' => '\1eople',
			'/(m)an$/i' => '\1en',
			'/(c)hild$/i' => '\1hildren',
			'/(buffal|tomat)o$/i' => '\1\2oes',
			'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
			'/us$/' => 'uses',
			'/(alias)$/i' => '\1es',
			'/(ax|cris|test)is$/i' => '\1es',
			'/s$/' => 's',
			'/^$/' => '',
			'/$/' => 's',
	),
		'uninflected' => array(
			'.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', 'people'
			),
		'irregular' => array(
			'atlas' => 'atlases',
			'beef' => 'beefs',
			'brother' => 'brothers',
			'child' => 'children',
			'corpus' => 'corpuses',
			'cow' => 'cows',
			'ganglion' => 'ganglions',
			'genie' => 'genies',
			'genus' => 'genera',
			'graffito' => 'graffiti',
			'hoof' => 'hoofs',
			'loaf' => 'loaves',
			'man' => 'men',
			'money' => 'monies',
			'mongoose' => 'mongooses',
			'move' => 'moves',
			'mythos' => 'mythoi',
			'niche' => 'niches',
			'numen' => 'numina',
			'occiput' => 'occiputs',
			'octopus' => 'octopuses',
			'opus' => 'opuses',
			'ox' => 'oxen',
			'penis' => 'penises',
			'person' => 'people',
			'sex' => 'sexes',
			'soliloquy' => 'soliloquies',
			'testis' => 'testes',
			'trilby' => 'trilbys',
			'turf' => 'turfs'
			)
			);

			/**
			 * Singular inflector rules
			 *
			 * @var array
			 * @access protected
			 **/
			private static $_singular = array(
		'rules' => array(
			'/(s)tatuses$/i' => '\1\2tatus',
			'/^(.*)(menu)s$/i' => '\1\2',
			'/(quiz)zes$/i' => '\\1',
			'/(matr)ices$/i' => '\1ix',
			'/(vert|ind)ices$/i' => '\1ex',
			'/^(ox)en/i' => '\1',
			'/(alias)(es)*$/i' => '\1',
			'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
			'/([ftw]ax)es/i' => '\1',
			'/(cris|ax|test)es$/i' => '\1is',
			'/(shoe|slave)s$/i' => '\1',
			'/(o)es$/i' => '\1',
			'/ouses$/' => 'ouse',
			'/uses$/' => 'us',
			'/([m|l])ice$/i' => '\1ouse',
			'/(x|ch|ss|sh)es$/i' => '\1',
			'/(m)ovies$/i' => '\1\2ovie',
			'/(s)eries$/i' => '\1\2eries',
			'/([^aeiouy]|qu)ies$/i' => '\1y',
			'/([lr])ves$/i' => '\1f',
			'/(tive)s$/i' => '\1',
			'/(hive)s$/i' => '\1',
			'/(drive)s$/i' => '\1',
			'/([^fo])ves$/i' => '\1fe',
			'/(^analy)ses$/i' => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
			'/([ti])a$/i' => '\1um',
			'/(p)eople$/i' => '\1\2erson',
			'/(m)en$/i' => '\1an',
			'/(c)hildren$/i' => '\1\2hild',
			'/(n)ews$/i' => '\1\2ews',
			'/^(.*us)$/' => '\\1',
			'/s$/i' => ''
			),
		'uninflected' => array(
			'.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', '.*ss'
			),
		'irregular' => array(
			'waves' => 'wave'
			)
			);

			/**
			 * Words that should not be inflected
			 *
			 * @var array
			 * @access protected
			 **/
			private static $_uninflected = array(
		'Amoyese', 'bison', 'Borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus',
		'carp', 'chassis', 'clippers', 'cod', 'coitus', 'Congoese', 'contretemps', 'corps',
		'debris', 'diabetes', 'djinn', 'eland', 'elk', 'equipment', 'Faroese', 'flounder',
		'Foochowese', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'graffiti',
		'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings',
		'jackanapes', 'Kiplingese', 'Kongoese', 'Lucchese', 'mackerel', 'Maltese', 'media',
		'mews', 'moose', 'mumps', 'Nankingese', 'news', 'nexus', 'Niasese',
		'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'pliers', 'Portuguese',
		'proceedings', 'rabies', 'rice', 'rhinoceros', 'salmon', 'Sarawakese', 'scissors',
		'sea[- ]bass', 'series', 'Shavese', 'shears', 'siemens', 'species', 'swine', 'testes',
		'trousers', 'trout','tuna', 'Vermontese', 'Wenchowese', 'whiting', 'wildebeest',
		'Yengeese'
		);

		/**
		 * Cached array identity map of pluralized words.
		 *
		 * @var array
		 * @access protected
		 **/
		private static $_pluralized = array();

		/**
		 * Cached array identity map of singularized words.
		 *
		 * @var array
		 * @access protected
		 **/
		private static $_singularized = array();

		/**
		 * Gets a reference to the Inflector object instance
		 *
		 * @return object
		 * @access public
		 */
		public static function getInstance() {
			static $instance = array();

			if (!$instance) {
				$instance[0] = new Celsus_Inflector();
			}
			return $instance[0];
		}

		/**
		 * Adds custom inflection $rules, of either 'plural' or 'singular' $type.
		 *
		 * @param string $type The type of inflection, either 'singular' or 'plural'
		 * @param array $rules Array of rules to be added. Example usage:
		 *					   Inflector::rules('plural', array('/^(inflect)or$/i' => '\1ables'));
		 *					   Inflector::rules('plural', array(
		 *							'rules' => array('/^(inflect)ors$/i' => '\1ables'),
		 *							'uninflected' => array('dontinflectme'),
		 *							'irregular' => array('red' => 'redlings')
		 *					   ));
		 * @access public
		 * @return void
		 * @static
		 */
		public static function rules($type, $rules = array()) {
			$type = '_'.$type;

			foreach ($rules as $rule => $pattern) {
				if (is_array($pattern)) {
			  self::${$type}[$rule] = array_merge($pattern, self::${$type}[$rule]);
			  unset($rules[$rule], self::${$type}['cache' . ucfirst($rule)], self::${$type}['merged'][$rule]);
				}
			}
			self::${$type}['rules'] = array_merge($rules, self::${$type}['rules']);

		}

		/**
		 * Return $word in plural form.
		 *
		 * @param string $word Word in singular
		 * @return string Word in plural
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function pluralize($word) {
			$_this = Celsus_Inflector::getInstance();

			if (isset(self::$_pluralized[$word])) {
				return self::$_pluralized[$word];
			}

			if (!isset(self::$_plural['merged']['irregular'])) {
				self::$_plural['merged']['irregular'] = self::$_plural['irregular'];
			}

			if (!isset(self::$_plural['merged']['uninflected'])) {
				self::$_plural['merged']['uninflected'] = array_merge(self::$_plural['uninflected'], self::$_uninflected);
			}

			if (!isset(self::$_plural['cacheUninflected']) || !isset(self::$_plural['cacheIrregular'])) {
				self::$_plural['cacheUninflected'] = '(?:' . join( '|', self::$_plural['merged']['uninflected']) . ')';
				self::$_plural['cacheIrregular'] = '(?:' . join( '|', array_keys(self::$_plural['merged']['irregular'])) . ')';
			}

			if (preg_match('/(.*)\\b(' . self::$_plural['cacheIrregular'] . ')$/i', $word, $regs)) {
				self::$_pluralized[$word] = $regs[1] . substr($word, 0, 1) . substr(self::$_plural['merged']['irregular'][strtolower($regs[2])], 1);
				return self::$_pluralized[$word];
			}

			if (preg_match('/^(' . self::$_plural['cacheUninflected'] . ')$/i', $word, $regs)) {
				self::$_pluralized[$word] = $word;
				return $word;
			}

			foreach (self::$_plural['rules'] as $rule => $replacement) {
				if (preg_match($rule, $word)) {
					self::$_pluralized[$word] = preg_replace($rule, $replacement, $word);
					return self::$_pluralized[$word];
				}
			}
		}

		/**
		 * Return $word in singular form.
		 *
		 * @param string $word Word in plural
		 * @return string Word in singular
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function singularize($word)
		{

			if (isset(self::$_singularized[$word])) {
				return self::$_singularized[$word];
			}

			if (!isset(self::$_singular['merged']['uninflected'])) {
				self::$_singular['merged']['uninflected'] = array_merge(self::$_singular['uninflected'], self::$_uninflected);
			}

			if (!isset(self::$_singular['merged']['irregular'])) {
				self::$_singular['merged']['irregular'] = array_merge(self::$_singular['irregular'], array_flip(self::$_plural['irregular']));
			}

			if (!isset(self::$_singular['cacheUninflected']) || !isset(self::$_singular['cacheIrregular'])) {
				self::$_singular['cacheUninflected'] = '(?:' . join( '|', self::$_singular['merged']['uninflected']) . ')';
				self::$_singular['cacheIrregular'] = '(?:' . join( '|', array_keys(self::$_singular['merged']['irregular'])) . ')';
			}

			if (preg_match('/(.*)\\b(' . self::$_singular['cacheIrregular'] . ')$/i', $word, $regs)) {
				self::$_singularized[$word] = $regs[1] . substr($word, 0, 1) . substr(self::$_singular['merged']['irregular'][strtolower($regs[2])], 1);
				return self::$_singularized[$word];
			}

			if (preg_match('/^(' . self::$_singular['cacheUninflected'] . ')$/i', $word, $regs)) {
				self::$_singularized[$word] = $word;
				return $word;
			}

			foreach (self::$_singular['rules'] as $rule => $replacement) {
				if (preg_match($rule, $word)) {
					self::$_singularized[$word] = preg_replace($rule, $replacement, $word);
					return self::$_singularized[$word];
				}
			}
			self::$_singularized[$word] = $word;
			return $word;
		}

		/**
		 * Returns the given lower_case_and_underscored_word as a CamelCased word.
		 *
		 * @param string $lower_case_and_underscored_word Word to camelize
		 * @return string Camelized word. LikeThis.
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function camelize($lowerCaseAndUnderscoredWord) {
			return str_replace(" ", "", ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord)));
		}

		/**
		 * Returns the given camelCasedWord as an underscored_word.
		 *
		 * @param string $camelCasedWord Camel-cased word to be "underscorized"
		 * @return string Underscore-syntaxed version of the $camelCasedWord
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function underscore($camelCasedWord) {
			return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
		}

		/**
		 * Returns the given underscored_word_group as a Human Readable Word Group.
		 * (Underscores are replaced by spaces and capitalized following words.)
		 *
		 * @param string $lower_case_and_underscored_word String to be made more readable
		 * @return string Human-readable string
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function humanize($lowerCaseAndUnderscoredWord) {
			return ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord));
		}

		/**
		 * Returns corresponding table name for given model $className. ("people" for the model class "Person").
		 *
		 * @param string $className Name of class to get database table name for
		 * @return string Name of the database table for given class
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function tableize($className) {
			return Inflector::pluralize(Inflector::underscore($className));
		}

		/**
		 * Returns Cake model class name ("Person" for the database table "people".) for given database table.
		 *
		 * @param string $tableName Name of database table to get class name for
		 * @return string Class name
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function classify($tableName) {
			return self::camelize(self::singularize($tableName));
		}

		/**
		 * Returns camelBacked version of an underscored string.
		 *
		 * @param string $string
		 * @return string in variable form
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function variable($string) {
			$string = Inflector::camelize(Inflector::underscore($string));
			$replace = strtolower(substr($string, 0, 1));
			return preg_replace('/\\w/', $replace, $string, 1);
		}

		/**
		 * Returns a string with all spaces converted to underscores (by default), accented
		 * characters converted to non-accented characters, and non word characters removed.
		 *
		 * @param string $string the string you want to slug
		 * @param string $replacement will replace keys in map
		 * @param array $map extra elements to map to the replacement
		 * @return string
		 * @access public
		 * @static
		 * @link http://book.cakephp.org/view/572/Class-methods
		 */
		public static function slug($string, $replacement = '_', $map = array()) {
			if (is_array($replacement)) {
				$map = $replacement;
				$replacement = '_';
			}

			$quotedReplacement = preg_quote($replacement, '/');

			$default = array(
			'/à|á|å|â/' => 'a',
			'/è|é|ê|ẽ|ë/' => 'e',
			'/ì|í|î/' => 'i',
			'/ò|ó|ô|ø/' => 'o',
			'/ù|ú|ů|û/' => 'u',
			'/ç/' => 'c',
			'/ñ/' => 'n',
			'/ä|æ/' => 'ae',
			'/ö/' => 'oe',
			'/ü/' => 'ue',
			'/Ä/' => 'Ae',
			'/Ü/' => 'Ue',
			'/Ö/' => 'Oe',
			'/ß/' => 'ss',
			'/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
			'/\\s+/' => $replacement,
			sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
			);

			$map = array_merge($default, $map);
			return preg_replace(array_keys($map), array_values($map), $string);
		}
}
?>