<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\DependencyInjection;

use Nette\Reflection\ClassReflection, 
	Nette\Environment;

/**
 * Dependency Injection container
 *
 * @author	Patrik Votoček
 */
class Context extends \Nette\FreezableObject implements \Nette\IContext, \ArrayAccess
{
	/** @var array */
	private $aliases = array();
	/** @var array */
	private $registry = array();
	/** @var array */
	private $factories = array();
	
	/**
	 * Adds the specified service to the service container
	 * 
	 * @param string
	 * @param mixed  object, class name or factory callback
	 * @param bool
	 * @param array
	 * @return Context
	 * @throws InvalidArgumentException
	 * @throws Nette\AmbiguousServiceException
	 * 
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function addService($name, $service, $singleton = TRUE, array $options = NULL)
	{
		$this->updating();
		if (!is_string($name) || $name === '') {
			throw new \InvalidArgumentException("Service name must be a non-empty string, " . gettype($name) . " given.");
		}

		$lower = strtolower($name);
		if (isset($this->registry[$lower])) { // only for instantiated services?
			throw new \Nette\AmbiguousServiceException("Service named '$name' has already been registered.");
		}
		if (isset($this->aliases[$lower])) { 
			throw new \Nette\AmbiguousServiceException("Service named '$name' is already used as a service alias.");
		}

		if ($service instanceof self) {
			$this->registry[$lower] = & $service->registry[$lower];
			$this->factories[$lower] = & $service->factories[$lower];

		} elseif (is_object($service) && !($service instanceof \Closure || $service instanceof \Nette\Callback)) {
			if (!$singleton || $options) {
				throw new \InvalidArgumentException("Service named '$name' is an instantiated object and must therefore be singleton without options.");
			}
			$this->registry[$lower] = $service;

		} else {
			if (!$service) {
				throw new \InvalidArgumentException("Service named '$name' is empty.");
			}
			$this->factories[$lower] = array($service, $singleton, $options);
			$this->registry[$lower] = & $this->factories[$lower][3]; // forces cloning using reference
		}
		
		return $this;
	}
	
	/**
	 * Add service alias
	 * 
	 * @param string
	 * @param string
	 * @return Context
	 * @throws InvalidArgumentException
	 * @throws Nette\AmbiguousServiceException
	 */
	public function addAlias($alias, $service)
	{
		$this->updating();
		
		if (!is_string($alias) || $alias === '') {
			throw new \InvalidArgumentException("Service alias name must be a non-empty string, " . gettype($alias) . " given.");
		}
		if (!is_string($service) || $service === '') {
			throw new \InvalidArgumentException("Service name must be a non-empty string, " . gettype($service) . " given.");
		}

		$lower = strtolower($service);

		if (!isset($this->registry[$lower]) && !isset($this->factories[$lower])) {
			throw new \InvalidArgumentException("Service '$service' not found.");
		} 
		
		$lowerA = strtolower($alias);
		if (isset($this->aliases[$lowerA])) {
			throw new \Nette\AmbiguousServiceException("Service alias named '$alias' has already been registered.");
		}
		if (isset($this->registry[$lowerA]) || isset($this->factories[$lowerA])) {
			throw new \Nette\AmbiguousServiceException("Service alias named '$alias' is already used as a service.");
		}
		
		$this->aliases[$lowerA] = $lower;
		
		return $this;
	}
	
	/**
	 * Gets the service object of the specified type
	 * 
	 * @param string
	 * @param array
	 * @return mixed
	 * @throws InvalidArgumentException
	 * @throws Nette\AmbiguousServiceException
	 * @throws InvalidStateException
	 * 
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function getService($name, array $options = NULL)
	{
		if (!is_string($name) || $name === '') {
			throw new \InvalidArgumentException("Service name must be a non-empty string, " . gettype($name) . " given.");
		}

		$lower = strtolower($name);
		
		if (isset($this->aliases[$lower])) {
			$lower = $this->aliases[$lower];
		}

		if (isset($this->registry[$lower])) { // instantiated singleton
			if ($options) {
				throw new \InvalidArgumentException("Service named '$name' is singleton and therefore can not have options.");
			}
			return $this->registry[$lower];

		} elseif (isset($this->factories[$lower])) {
			list($factory, $singleton, $defOptions) = $this->factories[$lower];

			if ($singleton && $options) {
				throw new \InvalidArgumentException("Service named '$name' is singleton and therefore can not have options.");

			} elseif ($defOptions) {
				$options = $options ? $options + $defOptions : $defOptions;
			}

			if (is_string($factory) && strpos($factory, ':') === FALSE) { // class name
				if (!class_exists($factory)) {
					throw new \Nette\AmbiguousServiceException("Cannot instantiate service '$name', class '$factory' not found.");
				}
				
				$reflection = ClassReflection::from($factory);
				
				if (isset($options['arguments']) && !$reflection->hasMethod('__construct')) {
					throw new \InvalidStateException("Service named '$name' does not have constructor.");
				} elseif (isset($options['arguments']) && $reflection->hasMethod('__construct')) {
					$service = $reflection->newInstanceArgs($this->processArgs($options['arguments']));
				} else {
					$service = new $factory;
				}

			} else { // factory callback
				$factory = callback($factory);
				if (!$factory->isCallable()) {
					throw new \InvalidStateException("Cannot instantiate service '$name', handler '$factory' is not callable.");
				}
				
				if (isset($options['arguments'])) {
					$service = $factory->invokeArgs($this->processArgs($options['arguments']));
				} else {
					$service = $factory();
				}
				
				if (!is_object($service)) {
					throw new \Nette\AmbiguousServiceException("Cannot instantiate service '$name', value returned by '$factory' is not object.");
				}
			}
			
			$reflection = ClassReflection::from(get_class($service));
			if (isset($options['callMethods'])) {
				foreach ($options['callMethods'] as $method => $args) {
					if (!$reflection->hasMethod($method)) {
						throw new \InvalidStateException("Unable to call method, method {$reflection->getName()}::$method() is missing.");
					}
					
					callback($service, $method)->invokeArgs($this->processArgs($args));
				}
			}

			if ($singleton) {
				$this->registry[$lower] = $service;
				unset($this->factories[$lower]);
			}
			return $service;

		} else {
			throw new \InvalidStateException("Service '$name' not found.");
		}
	}
	
	/**
	 * Process arguments for method, factory or constructor injection
	 * 
	 * - Convert @Service to service object
	 * - Convert %var% to environment variable
	 * - Convert $var to environment config
	 * 
	 * @param array
	 * @return array
	 */
	private function processArgs(array $args = NULL)
	{
		$args = $args === NULL ? array() : $args;
		$output = array();
		foreach ($args as $arg) {
			if (is_string($arg)) {
				if (\Nette\String::startsWith($arg, '@') && $this->hasService(substr($arg, 1))) {
					$output[] = $this->getService(substr($arg, 1));
				} elseif (\Nette\String::startsWith($arg, '%') && \Nette\String::endsWith($arg, '%')) { // @todo: better (DI) implementation
					$output[] = Environment::getVariable(substr($arg, 1, -1));
				} elseif (\Nette\String::startsWith($arg, '$')) {  // @todo: better (DI) implementation
					$output[] = Environment::getConfig(substr($arg, 1, -1));
				} else {
					$output[] = $arg;
				}
			} else {
				$output[] = (array) $arg;
			}
		}
		
		return $output;
	}
	
	/**
	 * @param Nette\Callback
	 * @return Nette\Reflection\MethodReflection|Nette\Reflection\FunctionReflection
	 */
	private function getFactoryReflection(\Nette\Callback $factory)
	{
		$factory = $factory->getNative();
		$factory = is_string($factory) ? explode("::", $factory) : $factory;
		if (count($factory) > 1) {
			return ClassReflection::from($factory[0])->getMethod($factory[1]);
		} else {
			return new \Nette\Reflection\FunctionReflection($factory[0]);
		}
	}
	
	/**
	 * Exists the service?
	 * 
	 * @param  string
	 * @param  bool
	 * @return bool
	 * @throws InvalidArgumentException
	 * 
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function hasService($name, $created = FALSE)
	{
		if (!is_string($name) || $name === '') {
			throw new \InvalidArgumentException("Service name must be a non-empty string, " . gettype($name) . " given.");
		}

		$lower = strtolower($name);
		return isset($this->registry[$lower]) || (!$created && isset($this->factories[$lower])) || isset($this->aliases[$lower]);
	}

	/**
	 * Removes the specified service type from the service container
	 * 
	 * @param string
	 * @return Context
	 * @throws InvalidArgumentException
	 * 
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function removeService($name)
	{
		$this->updating();
		if (!is_string($name) || $name === '') {
			throw new \InvalidArgumentException("Service name must be a non-empty string, " . gettype($name) . " given.");
		}

		$lower = strtolower($name);
		unset($this->registry[$lower], $this->factories[$lower]);
		
		return $this;
	}
	
	/**
	 * Exists the service?
	 * 
	 * @param string
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return $this->hasService($offset);
	}
	
	/**
	 * Gets the service object of the specified type
	 * 
	 * @param string
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->getService($offset);
	}
	
	/**
	 * Adds the specified service to the service container
	 * 
	 * @param string
	 * @param mixed
	 * @return Context
	 */
	public function offsetSet($offset, $value)
	{
		return $this->addService($offset, $value);
	}
	
	/**
	 * Removes the specified service type from the service container
	 * 
	 * @param string
	 * @return Context
	 */
	public function offsetUnset($offset)
	{
		return $this->removeService($offset);
	}
}