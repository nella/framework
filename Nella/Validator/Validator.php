<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Validator;

use Nette\Utils\Strings;

/**
 * Object property validator
 *
 * @author	Patrik Votoček
 *
 * @property-read IClassMetadataFactory $classMetadataFactory
 */
class Validator extends \Nette\Object implements IValidator
{
	const URL = 'url',
		EMAIL = 'email',
		TYPE = 'type',
		INSTANCE = 'instance',
		NOTNULL = 'notnull',
		MIN = 'min',
		MAX = 'max',
		MIN_LENGTH = 'minlength',
		MAX_LENGTH = 'maxlength',
		REGEXP = 'regexp';

	const NULLABLE = 'nullable';

	const CACHE_NAMESPACE = 'Nella.Validator.Metadata';

	/** @var array */
	private $validators = array(
		self::URL => array(array(__CLASS__, 'validateUrl'), "This value is not a valid URL"),
		self::EMAIL => array(array(__CLASS__, 'validateEmail'), "This value is not a valid e-mail"),
		self::TYPE => array(array(__CLASS__, 'validateType'), "This value should be type of %s"),
		self::INSTANCE => array(array(__CLASS__, 'validateInstance'), "This value should be instance of %s"),
		self::NOTNULL => array(array(__CLASS__, 'validateNotnull'), "This value should not be null"),
		self::MIN => array(array(__CLASS__, 'validateMin'), "This value should be %d or more"),
		self::MAX => array(array(__CLASS__, 'validateMax'), "This value should be %d or less"),
		self::MIN_LENGTH => array(array(__CLASS__, 'validateMinLength'), "This value is too short. It should have %d characters or more"),
		self::MAX_LENGTH => array(array(__CLASS__, 'validateMaxLength'), "This value is too long. It should have %d characters or less"),
		self::REGEXP => array(array(__CLASS__, 'validateRegexp'), "This value is not valid"),
	);
	/** @var IMetadataFactory */
	private $metadataFactory;

	/**
	 * @param IClassMetadataFactory
	 */
	public function __construct(IClassMetadataFactory $metadataFactory = NULL)
	{
		if (!$metadataFactory) {
			$metadataFactory = new ClassMetadataFactory;
		}

		$this->metadataFactory = $metadataFactory;
	}

	/**
	 * @return IClassMetadataFactory
	 */
	public function getClassMetadataFactory()
	{
		return $this->metadataFactory;
	}

	/**
	 * @param string
	 * @param \Nette\Callback
	 * @param string	error message
	 */
	public function addValidator($id, $callback, $message)
	{
		if (!is_string($id)) {
			throw new \Nette\InvalidArgumentException("Parameter key must be integer or string, " . gettype($id) . " given.");
		} elseif (!preg_match('#^[a-zA-Z0-9_]+$#', $id)) {
			throw new \Nette\InvalidArgumentException("Parameter key must be non-empty alphanumeric string, '$id' given.");
		}

		if (!is_callable($callback) && !($callback instanceof \Closure) && !($callback instanceof \Nette\Callback)) {
			throw new \Nette\InvalidArgumentException("Callback is not callable");
		}

		$this->validators[$id] = array($callback, $message);
		return $this;
	}

	/**
	 * @param string
	 * @return ClassMetadata
	 */
	protected function getClassMetadata($class)
	{
		return $this->getClassMetadataFactory()->getClassMetadata($class);
	}

	/**
	 * @param \Nette\Reflection\Property
	 * @param mixed
	 * @return mixed
	 */
	private function getValue(\Nette\Reflection\Property $reflection, $input)
	{
		$reflection->setAccessible(TRUE);
		return $reflection->getValue($input);
	}

	/**
	 * @param string
	 * @param mixed
	 * @return string
	 */
	private function getMessage($id, $data = NULL)
	{
		$message = $this->validators[$id][1];
		if ($data) {
			return vsprintf($message, (array) $data);
		}

		return $message;
	}

	/**
	 * @param mixed
	 * @return array
	 */
	public function validate($input, $class = NULL)
	{
		// Load metadata
		if ($class) {
			$metadata = $this->getClassMetadata($class);
		} else {
			$metadata = $this->getClassMetadata(get_class($input));
		}

		$errors = array();

		// Parse parent class first
		if ($metadata->parent) {
			$errors = $this->validate($input, $metadata->parent);
		}

		// validate object
		$reflection = $metadata->classReflection;
		foreach ($metadata->rules as $name => $rules) {
			$property = $reflection->getProperty($name);

			foreach ($rules as $rule) {
				// allowed null
				if (reset($rule) === NULL || reset($rule) == self::NULLABLE && $this->getValue($property, $input) === NULL) {
					unset($errors[$name]);
					break;
				} elseif (reset($rule) == self::NULLABLE) {
					continue;
				}

				// load value and parse aditional validator info
				$args = array($this->getValue($property, $input), $rule[1]);
				if ($rule[1] === NULL) {
					unset($args[1]);
				}

				if (!isset($this->validators[$rule[0]])) {
					throw new \Nette\InvalidStateException("Invalid validation rule '{$rule[0]}' not registered");
				}

				// validate property
				if (!callback($this->validators[$rule[0]][0])->invokeArgs($args)) {
					if (!isset($errors[$name]) || !is_array($errors[$name])) {
						$errors[$name] = array($this->getMessage($rule[0], $rule[1]));
					} else {
						$errors[$name][] = $this->getMessage($rule[0], $rule[1]);
					}
				}
			}
		}

		return $errors;
	}

	/******************************************************** validators *************************************************************/

	/**
	 * URL validator matching urls including port number and also relative urls
	 *
	 * @author Pavel Kučera
	 * @param string
	 * @return bool
	 */
	public static function validateUrl($url)
	{
		$chars = "a-z0-9\x80-\xFF";
		$isAbsoluteUrl = (bool) Strings::match($url, "#^(?:https?://|)(?:[$chars](?:[-$chars]{0,61}[$chars])?\\.)+[-$chars]{2,19}(?::([0-9]+))?(/\S*)?$#i");
		$isRelativeUrl = (bool) Strings::match($url, "#^/\S+$#i");
		$isValidIpUrl = (bool) Strings::match($url, "#^(?:https?://|)(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}(/\S*)?$#");

		return $isAbsoluteUrl || $isRelativeUrl || $isValidIpUrl;
	}

	/**
	 * Email validator
	 *
	 * @author David Grudl
	 * @param string
	 * @return bool
	 */
	public static function validateEmail($email)
	{
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$localPart = "(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)"; // quoted or unquoted
		$chars = "a-z0-9\x80-\xFF"; // superset of IDN
		$domain = "[$chars](?:[-$chars]{0,61}[$chars])"; // RFC 1034 one domain component
		return (bool) Strings::match($email, "(^$localPart@(?:$domain?\\.)+[-$chars]{2,19}\\z)i");
	}

	/**
	 * Validate input type
	 *
	 * @param mixed
	 * @param string
	 * @return bool
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function validateType($input, $type)
	{
		switch ($type) {
			case 'string':
				return is_string($input);
				break;
			case 'int':
			case 'integer':
				return (bool) Strings::match($input, '/^-?[0-9]+$/');
				break;
			case 'float':
			case 'double':
				return (bool) Strings::match($input, '/^-?[0-9]*[.,]?[0-9]+$/');
				break;
			case 'bool':
			case 'boolean':
				return is_bool($input);
				break;
			case 'array':
				return is_array($input);
				break;
			case 'scalar':
				return is_scalar($input);
				break;
			case 'object':
				return is_object($input);
				break;
			case 'null':
				return is_null($input);
				break;
			default:
				throw new \Nette\InvalidArgumentException("Unsupported validation type");
				break;
		}
	}

	/**
	 * Validate instance
	 *
	 * @param mixed
	 * @param string
	 * @return bool
	 */
	public static function validateInstance($input, $name)
	{
		if ($input instanceof $name) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Validate not null
	 *
	 * @param mixed
	 * @return bool
	 */
	public static function validateNotNull($input)
	{
		if (is_string($input)) {
			$input = trim($input);
			$input = $input === "" ? NULL : $input;
		}

		return !is_null($input);
	}

	/**
	 * @param mixed
	 * @param int
	 * @return bool
	 */
	public static function validateMin($input, $limit)
	{
		return $input >= $limit;
	}

	/**
	 * Validate max
	 *
	 * @param mixed
	 * @param int
	 * @return bool
	 */
	public static function validateMax($input, $limit)
	{
		return $input <= $limit;
	}

	/**
	 * Validate min length
	 *
	 * @param mixed
	 * @param int
	 * @return bool
	 */
	public static function validateMinLength($input, $limit)
	{
		return strlen($input) >= $limit;
	}

	/**
	 * Validate max length
	 *
	 * @param mixed
	 * @param int
	 * @return bool
	 */
	public static function validateMaxLength($input, $limit)
	{
		return strlen($input) <= $limit;
	}

	/**
	 * Validate regexp
	 *
	 * @param mixed
	 * @param string
	 * @return bool
	 */
	public static function validateRegexp($input, $pattern)
	{
		return (bool) Strings::match($input, $pattern);
	}
}