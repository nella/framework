<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\DependencyInjection;

/**
 * Dependency injection service factory
 *
 * @author	Patrik Votoček
 * 
 * @property-read string $name
 * @property-write string $class
 * @property-write \Nette\Callback $factory
 * @property-write array $arguments
 * @property-write array $methods
 * @property bool $singleton
 * @property-read $instance
 */
class ServiceFactory extends \Nette\Object implements IServiceFactory
{
	/** @var IContext */
	protected $context;
	/** @var string */
	protected $name;
	/** @var string */
	protected $class;
	/** @var \Nette\Callback */
	protected $factory;
	/** @var array */
	protected $arguments;
	/** @var array */
	protected $methods;
	/** @var bool */
	protected $singleton;
	/*p @var \Nette\Callback *
	protected $configurator;
	 */
	
	/** @var array */
	public $onInit = array();
	/** @var array */
	public $onReturn = array();
	
	/**
	 * @param IContexy
	 * @param string
	 */
	public function __construct(IContext $context, $name)
	{
		$this->context = $context;
		$this->name = $name;
		$this->singleton = TRUE;
		$this->arguments = $this->methods = array();
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param string
	 * @return ServiceFactory
	 */
	public function setClass($class)
	{
		if (!is_string($class) && !$this->singleton) {
			throw new \InvalidArgumentException("Non sigleton allow only for factory or class");
		}
		$this->class = $class;
		return $this;
	}
	
	/**
	 * @param string
	 * @return ServiceFactory
	 * @throws InvalidArgumentException
	 */
	public function setFactory($factory)
	{
		if (is_string($factory) && strpos($factory, "::") !== FALSE) {
			$factory = callback($factory);
		}
		
		if (!is_callable($factory) && !($factory instanceof \Closure) && !($factory instanceof \Nette\Callback)) {
			throw new \InvalidArgumentException("Factory must be valid callback");
		}
		
		$this->factory = $factory;
		return $this;
	}
	
	/**
	 * @param array
	 * @return ServiceFactory
	 */
	public function setArguments(array $arguments = NULL)
	{
		$arguments = $arguments === NULL ? array() : $arguments;
		$this->arguments = $arguments;
		return $this;
	}
	
	/**
	 * @param mixed
	 * @return ServiceFactory 
	 */
	public function addArgument($value)
	{
		$this->arguments[] = $value;
		return $this;
	}
	
	/**
	 * @param array
	 * @return ServiceFactory
	 */
	public function setMethods(array $methods = NULL)
	{
		$methods = $methods === NULL ? array() : $methods;
		$this->methods = $methods;
		return $this;
	}
	
	/**
	 * @param string
	 * @param array
	 * @return ServiceFactory
	 */
	public function addMethod($name, array $arguments = NULL)
	{
		$arguments = $arguments === NULL ? array() : $arguments;
		$this->methods[] = array('method' => $name, 'arguments' => $arguments);
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isSingleton()
	{
		return $this->singleton;
	}
	
	/**
	 * @param bool
	 * @return ServiceFactory
	 */
	public function setSingleton($singleton)
	{
		$this->singleton = $singleton;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	protected function createInstance()
	{
		if (is_string($this->class)) { // Class
			if (!class_exists($this->class)) {
				throw new \InvalidStateException("Class '{$this->class}' not exist");
			}
			$ref = new \Nette\Reflection\ClassReflection($this->class);
			$args = $this->context->expandParameter($this->arguments);
			if ($args) {
				$instance = $ref->newInstanceArgs($args);
			} else {
				$instance = $ref->newInstanceArgs();
			}
			
			return $instance;
		} elseif ($this->class) { // Instance
			if (!$this->singleton) {
				throw new \InvalidStateException("Non sigleton allow only for factory or class");
			}
			return $this->class;
		} elseif ($this->factory) { // Factory
			return callback($this->factory)->invokeArgs($this->context->expandParameter($this->arguments));
		} else {
			throw new \InvalidStateException("Class or factory not defined");
		}
	}
	
	/**
	 * @param mixed
	 */
	protected function callMethods($instance)
	{
		foreach ($this->methods as $value) {
			callback($instance, $value['method'])->invokeArgs($this->context->expandParameter($value['arguments']));
		}
	}
	
	/**
	 * @return mixed
	 */
	public function getInstance()
	{
		$instance = $this->createInstance();
		$this->onInit($instance);
		
		if ($this->class && $this->factory) { // if defined class and factroy use factory as "configurator"
			callback($this->factory)->invokeArgs(array($instance));
		}
		
		$this->callMethods($instance);
		
		$this->onReturn($instance);
		return $instance;
	}
}
