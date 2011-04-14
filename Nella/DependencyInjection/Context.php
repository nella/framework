<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\DependencyInjection;

use Nette\Environment, 
	Nette\StringUtils;

/**
 * Dependency injection service container
 *
 * @author	Patrik Votoček
 *
 * @property string $environment
 */
class Context extends \Nette\FreezableObject implements IContext, \ArrayAccess
{
	/** @var string */
	private $environment;
	/** @var array */
	private $parameters = array();
	/** @var array */
	private $aliases = array();
	/** @var array */
	private $registry = array();
	/** @var array */
	private $globalRegistry = array();
	/** @var array */
	private $factories = array();

	public function __construct()
	{
		$lower = strtolower('Nella\DependencyInjection\IContext');
		$this->registry[$lower] = & $this->globalRegistry[$lower];
		$this->registry[$lower] = $this;
	}

	/**
	 * @return string
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}

	/**
	 * @param string
	 * @return Context
	 * @throws \Nette\InvalidStateException
	 */
	public function setEnvironment($environment)
	{
		if ($this->isFrozen() && $environment != $this->environment) {
			throw new \Nette\InvalidStateException("Service container is frozen and cannot be changed");
		}

		$this->environment = $environment;
		return $this;
	}

	/**
	 * @param string
	 * @param mixed
	 * @return Context
	 * @throws \Nette\InvalidStateException
	 * @throws \Nette\InvalidArgumentException
	 */
	public function setParameter($key, $value)
	{
		if ($this->isFrozen()) {
			throw new \Nette\InvalidStateException("Service container is frozen for changes");
		}

		if (!is_string($key)) {
			throw new \Nette\InvalidArgumentException("Parameter key must be integer or string, " . gettype($key) . " given.");
		} elseif (!preg_match('#^[a-zA-Z0-9_]+$#', $key)) {
			throw new \Nette\InvalidArgumentException("Parameter key must be non-empty alphanumeric string, '$key' given.");
		}

		$this->parameters[$key] = $value;
		return $this;
	}

	/**
	 * @param string
	 * @return bool
	 */
	public function hasParameter($key)
	{
		if (array_key_exists($key, $this->parameters)) {
			return TRUE;
		}

		$const = strtoupper(preg_replace('#(.)([A-Z]+)#', '$1_$2', $key));
		$list = get_defined_constants(TRUE);
		if (array_key_exists('user' , $list) && array_key_exists($const, $list['user'])) {
			$this->parameters[$key] = $list['user'][$const];
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @internal
	 * @param mixed
	 * @return mixed
	 */
	public function expandParameter($data)
	{
		if (is_array($data) || $data instanceof \ArrayAccess) {
			$tmp = array();
			foreach ($data as $key => $value) {
				$tmp[$key] = $this->expandParameter($value);
			}
			$data = $tmp;
		} else {
			if (is_string($data)) {
				if (StringUtils::startsWith($data, '@') && $this->hasService(substr($data, 1))) {
					$data = $this->getService(substr($data, 1));
				} elseif (StringUtils::startsWith($data, '%') && StringUtils::endsWith($data, '%')) { // @todo: better (DI) implementation
					$data = $this->getParameter(substr($data, 1, -1));
				}
			}
		}

		return Environment::expand($data);
	}

	/**
	 * @param string
	 * @return mixed
	 * @throws \Nette\InvalidStateException
	 */
	public function getParameter($key)
	{
		if (!$this->hasParameter($key)) {
			throw new \Nette\InvalidStateException("Unknown context parameter '$key'.");
		}
		return $this->expandParameter($this->parameters[$key]);
	}

	/**
	 * Adds the specified service to the service container
	 *
	 * @param string
	 * @param mixed  object, class name or factory callback
	 * @param bool
	 * @param array
	 * @return Context
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\DI\AmbiguousServiceException
	 *
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function addService($name, $service, $singleton = TRUE, array $options = NULL)
	{
		if ($this->isFrozen()) {
			throw new \Nette\InvalidStateException("Service container is frozen and cannot be changed");
		}

		if (!is_string($name) || $name === '') {
			throw new \Nette\InvalidArgumentException("Service name must be a non-empty string, " . gettype($name) . " given.");
		}

		$lower = strtolower($name);
		if (isset($this->registry[$lower])) { // only for instantiated services?
			throw new \Nette\DI\AmbiguousServiceException("Service named '$name' has already been registered.");
		}
		if (isset($this->aliases[$lower])) {
			throw new \Nette\DI\AmbiguousServiceException("Service named '$name' is already used as a service alias.");
		}

		if ($service instanceof self) {
			$this->registry[$lower] = & $service->registry[$lower];
			$this->factories[$lower] = & $service->factories[$lower];

		} elseif (is_object($service) && !($service instanceof \Closure || $service instanceof \Nette\Callback)) {
			if (!$singleton || $options) {
				throw new \Nette\InvalidArgumentException("Service named '$name' is an instantiated object and must therefore be singleton without options.");
			}
			$this->registry[$lower] = $service;

		} else {
			if (!$service) {
				throw new \Nette\InvalidArgumentException("Service named '$name' is empty.");
			}

			$factory = new ServiceFactory($this, $name);
			$factory->singleton = $singleton;

			// BACKWARD COMPATABILITY
			if ((is_string($service) && strpos($service, '::') !== FALSE) || $service instanceof \Closure ||
					is_callable($service) || $service instanceof \Nette\Callback) {
				$factory->factory = $service;
			} elseif ($service) {
				$factory->class = $service;
			}

			if (isset($options['class'])) {
				$factory->class = $options['class'];
			}
			if (isset($options['factory'])) {
				$factory->factory = $options['factory'];
			}
			if (isset($options['arguments'])) {
				$factory->arguments = $options['arguments'];
			}
			if (isset($options['methods'])) {
				$factory->methods = $options['methods'];
			}
			
			$this->addFactory($factory);
			
			if (isset($options['aliases']) && count($options['aliases'])) {
				foreach ($options['aliases'] as $alias) {
					$this->addAlias($alias, $lower);
				}
			}
		}

		return $this;
	}

	/**
	 * Add service alias
	 *
	 * @param string
	 * @param string
	 * @return Context
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\DI\AmbiguousServiceException
	 */
	public function addAlias($alias, $service)
	{
		if ($this->isFrozen()) {
			throw new \Nette\InvalidStateException("Service container is frozen for changes");
		}

		if (!is_string($alias) || $alias === '') {
			throw new \Nette\InvalidArgumentException("Service alias name must be a non-empty string, " . gettype($alias) . " given.");
		}
		if (!is_string($service) || $service === '') {
			throw new \Nette\InvalidArgumentException("Service name must be a non-empty string, " . gettype($service) . " given.");
		}

		$lower = strtolower($service);

		if (!isset($this->registry[$lower]) && !isset($this->factories[$lower])) {
			throw new \Nette\InvalidArgumentException("Service '$service' not found.");
		}

		$lowerA = strtolower($alias);
		if (isset($this->aliases[$lowerA])) {
			throw new \Nette\DI\AmbiguousServiceException("Service alias named '$alias' has already been registered.");
		}
		if (isset($this->registry[$lowerA]) || isset($this->factories[$lowerA])) {
			throw new \Nette\DI\AmbiguousServiceException("Service alias named '$alias' is already used as a service.");
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
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\DI\AmbiguousServiceException
	 * @throws \Nette\InvalidStateException
	 *
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function getService($name, array $options = NULL)
	{
		$lower = strtolower($name);

		if (isset($this->aliases[$lower])) {
			$lower = $this->aliases[$lower];
		}

		if (isset($this->registry[$lower])) { // instantiated singleton
			if ($options) {
				throw new \Nette\InvalidArgumentException("Service named '$name' is singleton and therefore can not have options.");
			}
			return $this->registry[$lower];

		} elseif (isset($this->factories[$lower])) {
			$factory = $this->factories[$lower];

			if (isset($options['arguments'])) {
				$factory->arguments = $options['arguments'];
			}
			if (isset($options['methods'])) {
				$factory->methods = $options['methods'];
			}

			$service = $factory->instance;

			if ($factory->singleton) {
				$this->registry[$lower] = $service;
				unset($this->factories[$lower]);
			}
			return $service;
		} else {
			throw new \Nette\InvalidStateException("Service '$name' not found.");
		}
	}

	/**
	 * Exists the service?
	 *
	 * @param  string
	 * @param  bool
	 * @return bool
	 * @throws \Nette\InvalidArgumentException
	 *
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function hasService($name, $created = FALSE)
	{
		$lower = strtolower($name);
		return isset($this->registry[$lower]) || (!$created && isset($this->factories[$lower])) || isset($this->aliases[$lower]);
	}

	/**
	 * @param string
	 * @return IServiceFactory
	 */
	public function getFactory($name)
	{
		$lower = strtolower($name);
		if (!isset($this->factories[$lower])) {
			throw new \Nette\InvalidStateException("Service factory '$name' not found.");
		}

		return $this->factories[$lower];
	}

	/**
	 * @param IServiceFactory
	 * @return Context
	 * @throws \Nette\InvalidStateException
	 * @throws \Nette\DI\AmbiguousServiceException
	 */
	public function addFactory(IServiceFactory $factory)
	{
		if ($this->isFrozen()) {
			throw new \Nette\InvalidStateException("Service container is frozen for changes");
		}

		$lower = strtolower($factory->getName());
		if (isset($this->registry[$lower])) { // only for instantiated services?
			throw new \Nette\DI\AmbiguousServiceException("Service named '{$factory->getName()}' has already been registered.");
		}
		if (isset($this->aliases[$lower])) {
			throw new \Nette\DI\AmbiguousServiceException("Service named '{$factory->getName()}' is already used as a service alias.");
		}

		$this->factories[$lower] = $factory;
		$this->registry[$lower] = & $this->globalRegistry[$lower]; // forces cloning using reference

		return $this;
	}

	/**
	 * Removes the specified service type from the service container
	 *
	 * @param string
	 * @return Context
	 * @throws \Nette\InvalidArgumentException
	 *
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function removeService($name)
	{
		if ($this->isFrozen()) {
			throw new \Nette\InvalidStateException("Service container is frozen for changes");
		}

		if (!is_string($name) || $name === '') {
			throw new \Nette\InvalidArgumentException("Service name must be a non-empty string, " . gettype($name) . " given.");
		}

		$lower = strtolower($name);
		unset($this->registry[$lower], $this->factories[$lower]);

		return $this;
	}

	/**
	 * Does the service exist?
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
