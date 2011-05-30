<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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
	const DEFAULT_NAMESPACE = 'App';

	/** @var Nette\DI\IContainer */
	private $container;

	/**
	 * @param Nette\DI\IContainer
	 */
	public function __construct(\Nette\DI\IContainer $container)
	{
		$this->container = $container;
	}

	/**
	 * Create new presenter instance.
	 * @param  string  presenter name
	 * @return IPresenter
	 */
	public function createPresenter($name)
	{
		$class = $this->getPresenterClass($name);
		$presenter = new $class;
		$presenter->setContext($this->container);
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
		$namespaces = isset($this->container->params['namespaces'])
			 ? $this->container->params['namespaces']
			 : array(static::DEFAULT_NAMESPACE);
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
		return $namespace . "\\" . str_replace(':', "\\", $presenter.'Presenter');
	}

	/**
	 * Formats presenter name from class name.
	 *
	 * @param string presenter class
	 * @return string
	 */
	public function unformatPresenterClass($class)
	{
		$active = "";
		$namespaces = isset($this->container->params['namespaces'])
			 ? $this->container->params['namespaces']
			 : array(static::DEFAULT_NAMESPACE);
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
			return str_replace("\\", ':', substr($class, strlen($active), -9));
		} else {
			return str_replace("\\", ':', substr($class, 0, -9));
		}
	}
}
