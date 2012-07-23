<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella;

use Nette\Reflection\ClassType,
	Nette\Reflection\Property,
	Nette\Utils\Strings,
	Nette\MemberAccessException;

/**
 * Nella ObjectMixin
 *
 * @author	Patrik Votoček
 */
class ObjectMixin extends \Nette\Object
{
	/** @var array */
	private static $properties = array();
	/** @var array */
	private static $basicTypes = array(
		"string", "integer", "int", "boolean", "bool", "float", "double", "object", 
		"mixed", "resource", "void", "callback", "false", "true", "callable"
	);
	
	/**
	 * @param string
	 */
	private static function parse($class)
	{
		$ref = ClassType::from($class);
		static::$properties[$class] = array();
		foreach ($ref->getAnnotations() as $annotation => $annotations) {
			foreach ($annotations as $values) {
				$data = Strings::split($values, '/[\s]+/');
				if (!in_array($annotation, array('property', 'property-read', 'property-write'))) {
					continue;
				} elseif (!isset($data[0]) || !isset($data[1]) || !Strings::startsWith($data[1], '$')) {
					continue;
				} elseif (isset(static::$properties[$class][Strings::substring($data[1], 1)])) {
					throw new \Nette\InvalidStateException("Duplicate property {$data[1]} definition");
				} else {
					$name = Strings::substring($data[1], 1);
					static::$properties[$class][$name] = array(
						'mode' => $annotation == 'property-read' ? 'read' : ($annotation == 'property-write' ? 'write' : 'all'),
						'types' => array(),
						'origTypes' => $data[0],
					);
					foreach (explode('|', $data[0]) as $type) {
						$tmp = trim(Strings::lower($type));
						if ($tmp == 'self') {
							static::$properties[$class][$name]['types'][] = $ref->getName();
						} elseif (in_array($tmp, static::$basicTypes) || in_array($tmp, array('null', 'array'))) {
							static::$properties[$class][$name]['types'][] = $tmp;
						} elseif (Strings::startsWith($type, '\\')) {
							static::$properties[$class][$name]['types'][] = trim(Strings::substring($type, 1));
						} elseif (Strings::endsWith($type, '[]')) {
							static::$properties[$class][$name]['types'][] = 'arrayobj';
						} else {
							static::$properties[$class][$name]['types'][] = trim($ref->getNamespaceName() . '\\' . $type);
						}
					}
				}
			}
		}
	}
	
	/**
	 * @param string
	 * @param string
	 * @param string
	 * @param \Nette\MemberAccessException
	 * @throws \Nette\MemberAccessException
	 * @return \Nette\Reflection\Property
	 */
	private static function getPropertyReflection($class, $name, $mode = 'all', MemberAccessException $e)
	{
		$ref = ClassType::from($class);
		if (!isset(static::$properties[$class])) {
			static::parse($class);
		}
		$metadata = static::$properties[$class];
		
		if (!isset($metadata[$name]) || !in_array($metadata[$name]['mode'], array($mode, 'all')) || !$ref->hasProperty($name)) {
			throw $e;
		}
		
		return $ref->getProperty($name);
	}
	
	/**
	 * @param \Nette\Reflection\Property
	 * @param mixed
	 * @throws \Nette\FatalErrorException
	 */
	private static function validateValue(Property $ref, $value)
	{
		$classRef = $ref->getDeclaringClass();
		$metadata = static::$properties[$classRef->getName()][$ref->getName()];
		
		$failed = TRUE;
		foreach ($metadata['types'] as $type) {
			if (in_array($type, static::$basicTypes)) { // basic
				$failed = FALSE;
			} elseif (is_object($value) && $value instanceof $type) { // class
				$failed = FALSE;
			} elseif ($type == 'null' && is_null($value)) { // null
				$failed = FALSE;
			} elseif ($type == 'array' && is_array($value)) { // array
				$failed = FALSE;
			}
		}
		
		if ($failed) {
			$trace = debug_backtrace();
			$type = is_object($value) ? ('instance of ' . get_class($value)) : gettype($value);
			$message = "Property {$classRef->getName()}::\${$ref->getName()} must be an {$metadata['origTypes']}, {$type} given";
			if (isset($trace[3]['file'])) {
				$message .= ', called in '.$trace[3]['file'];
			}
			if (isset($trace[3]['line'])) {
				$message .= ' on line '.$trace[3]['line'];
			}
			throw new \Nette\FatalErrorException(
				$message, 0, E_RECOVERABLE_ERROR, isset($trace[3]['file']) ? $trace[3]['file'] : $classRef->getFileName(), isset($trace[3]['line']) ? $trace[3]['line'] : NULL, NULL
			);
		}
	}
	
	/**
	 * @param object
	 * @param \Nette\Reflection\Property
	 * @param mixed
	 * @param \Nette\MemberAccessException
	 * @throws \Nette\MemberAccessException
	 * @return mixed
	 */
	private static function & getter($_this, Property $ref, MemberAccessException $e)
	{
		$ref->setAccessible(TRUE);
		$value = $ref->getValue($_this);
		return $value;
	}
	
	/**
	 * @param object
	 * @param \Nette\Reflection\Property
	 * @param mixed
	 * @param \Nette\MemberAccessException
	 * @throws \Nette\MemberAccessException
	 * @return object
	 */
	private static function setter($_this, Property $ref, $value, MemberAccessException $e)
	{
		$ref->setAccessible(TRUE);
		static::validateValue($ref, $value);
		$ref->setValue($_this, $value);
		return $_this;
	}
	
	/**
	 * Call to undefined method
	 * 
	 * @param  object
	 * @param  string  method name
	 * @param  array   arguments
	 * @return mixed
	 * @throws \Nette\MemberAccessException
	 */
	public static function call($_this, $name, $args)
	{
		try {
			return \Nette\ObjectMixin::call($_this, $name, $args);
		} catch (\Nette\MemberAccessException $e) {
			if (count($args) > 1) {
				throw $e;
			} elseif (Strings::startsWith($name, 'get') || Strings::startsWith($name, 'set')) {
				$class = get_class($_this);
				$var = Strings::lower(Strings::substring($name, 3, 1)) . Strings::substring($name, 4);
				if (Strings::startsWith($name, 'get')) {
					$ref = static::getPropertyReflection($class, $var, 'read', $e);
					return static::getter($_this, $ref, $e);
				} else {
					$ref = static::getPropertyReflection($class, $var, 'write', $e);
					return static::setter($_this, $ref, $args[0], $e);
				}
			} else {
				throw $e;
			}
		}
	}
	
	/**
	 * Returns property value
	 * 
	 * @param  object
	 * @param  string  property name
	 * @return mixed   property value
	 * @throws \Nette\MemberAccessException if the property is not defined.
	 */
	public static function & get($_this, $name)
	{
		try {
			return \Nette\ObjectMixin::get($_this, $name);
		} catch (\Nette\MemberAccessException $e) {
			$class = get_class($_this);
			$ref = static::getPropertyReflection($class, $name, 'read', $e);
			return static::getter($_this, $ref, $e);
		}
	}
	
	/**
	 * Sets value of a property
	 * 
	 * @param  object
	 * @param  string  property name
	 * @param  mixed   property value
	 * @return void
	 * @throws \Nette\MemberAccessException if the property is not defined or is read-only
	 */
	public static function set($_this, $name, $value)
	{
		try {
			return \Nette\ObjectMixin::set($_this, $name, $value);
		} catch (\Nette\MemberAccessException $e) {
			$class = get_class($_this);
			$ref = static::getPropertyReflection($class, $name, 'write', $e);
			return static::setter($_this, $ref, $value, $e);
		}
	}
	
	/**
	 * Is property defined?
	 * 
	 * @param  object
	 * @param  string  property name
	 * @return bool
	 */
	public static function has($_this, $name)
	{
		$has = \Nette\ObjectMixin::has($_this, $name);
		if ($has) {
			return TRUE;
		} else {
			$class = get_class($_this);
			if (!isset(static::$properties[$class])) {
				static::parse($class);
			}
			if (!isset(static::$properties[$class][$name]) || !in_array(static::$properties[$class][$name]['mode'], array('read', 'all'))) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}
}

