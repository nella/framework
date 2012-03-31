<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Application;

use Nette\Utils\Strings;

/**
 * Nella presenter factory
 *
 * @author	Patrik Votoček
 */
class PresenterFactory extends \Nette\Object implements \Nette\Application\IPresenterFactory
{
	const DEFAULT_NAMESPACE = 'App',
		MODULE_SUFFIX = 'Module';

	/** @var bool */
	public $useModuleSuffix = TRUE;
	/** @var Nette\DI\IContainer */
	private $container;
	/** @var \SplPriorityQueue */
	private $namespaces;

	/**
	 * @param Nette\DI\IContainer
	 */
	public function __construct(\Nette\DI\IContainer $container)
	{
		$this->container = $container;
		$this->namespaces = new \SplPriorityQueue;
		$this->namespaces->insert(static::DEFAULT_NAMESPACE, 2);
	}

	/**
	 * @param string
	 * @param int
	 * @return PresenterFactory
	 */
	public function addNamespace($namespace, $priority = 5)
	{
		$this->namespaces->insert($namespace, $priority);
		return $this;
	}

	/**
	 * Create new presenter instance.
	 * @param  string  presenter name
	 * @return IPresenter
	 */
	public function createPresenter($name)
	{
		$presenter = $this->container->createInstance($this->getPresenterClass($name));
		if (method_exists($presenter, 'setContext')) {
			$this->container->callMethod(array($presenter, 'setContext'));
		}
		return $presenter;
	}

	/**
	 * Format presenter class with prefixes
	 *
	 * @param string
	 * @return string
	 * @throws \Nette\Application\InvalidPresenterException
	 */
	private function formatPresenterClasses($name)
	{
		$class = NULL;
		$namespaces = clone $this->namespaces;
		foreach ($namespaces as $namespace) {
			$class = $this->formatPresenterClass($name, $namespace);
			if (class_exists($class)) {
				break;
			}
		}

		if (!class_exists($class)) {
			$class = $this->formatPresenterClass($name, reset($namespaces));
			throw new \Nette\Application\InvalidPresenterException("Cannot load presenter '$name', class '$class' was not found.");
		}

		return $class;
	}

	/**
	 * Get presenter class name
	 *
	 * @param string
	 * @return string
	 * @throws \Nette\Application\InvalidPresenterException
	 */
	public function getPresenterClass(& $name)
	{
		if (!is_string($name) || !preg_match("#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*$#", $name)) {
			throw new \Nette\Application\InvalidPresenterException("Presenter name must be an alphanumeric string, '$name' is invalid.");
		}

		$class = $this->formatPresenterClasses($name);
		$reflection = \Nette\Reflection\ClassType::from($class);
		$class = $reflection->getName();

		if (!$reflection->implementsInterface('Nette\Application\IPresenter')) {
			throw new \Nette\Application\InvalidPresenterException("Cannot load presenter '$name', class '$class' is not Nette\\Application\\IPresenter implementor.");
		}
		if ($reflection->isAbstract()) {
			throw new \Nette\Application\InvalidPresenterException("Cannot load presenter '$name', class '$class' is abstract.");
		}

		// canonicalize presenter name
		$realName = $this->unformatPresenterClass($class);
		if ($name !== $realName) {
			throw new \Nette\Application\InvalidPresenterException("Cannot load presenter '$name', case mismatch. Real name is '$realName'.");
		}

		return $class;
	}


	/**
	 * Formats presenter class name from its name.
	 *
	 * @param string presenter name
	 * @param string
	 * @return string
	 */
	public function formatPresenterClass($presenter, $namespace = 'App')
	{
		if ($presenter == 'Nette:Micro') {
			return 'NetteModule\MicroPresenter';
		}

		return $namespace . "\\" . str_replace(':', ($this->useModuleSuffix ? self::MODULE_SUFFIX : "")."\\", $presenter.'Presenter');
	}

	/**
	 * Formats presenter name from class name.
	 *
	 * @param string presenter class
	 * @return string
	 */
	public function unformatPresenterClass($class)
	{
		if ($class == 'NetteModule\MicroPresenter') {
			return 'Nette:Micro';
		}

		$active = "";
		$namespaces = clone $this->namespaces;
		foreach ($namespaces as $namespace) {
			if (Strings::startsWith($class, $namespace)) {
				$current = $namespace . "\\";
				if (!$active || strlen($active) < strlen($current)) {
					$active = $current;
				}
			}
		}

		$class = Strings::startsWith('\\', $class) ? substr($class, 1) : $class;
		if (strlen($active)) {
			return str_replace(($this->useModuleSuffix ? self::MODULE_SUFFIX : "")."\\", ':', substr($class, strlen($active), -9));
		} else {
			return str_replace(($this->useModuleSuffix ? self::MODULE_SUFFIX : "")."\\", ':', substr($class, 0, -9));
		}
	}
}
