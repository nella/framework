<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application;

/**
 * Nella presenter factory
 *
 * @author	Patrik Votoček
 */
class PresenterFactory extends \Nette\Application\PresenterFactory
{
	/** @var \Nella\FreezableArray */
	private $registry;

	/**
	 * @param string
	 * @param \Nette\DI\IContext
	 */
	public function __construct($baseDir, \Nette\DI\IContext $context)
	{
		$this->registry = $context->getService('Nella\Registry\NamespacePrefixes');
		parent::__construct($baseDir, $context);
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
		$prefixes = (array) $this->registry->getIterator();
		foreach (array_keys($prefixes) as $key) {
			$class = $this->formatPresenterClass($name, $key);
			if (class_exists($class)) {
				break;
			}
		}

		if (!class_exists($class)) {
			$class = $this->formatPresenterClass($name);
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
			if ($this->caseSensitive) {
				throw new \Nette\Application\InvalidPresenterException("Cannot load presenter '$name', case mismatch. Real name is '$realName'.");
			}
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
	public function formatPresenterClass($presenter, $type = 'app')
	{
		if (isset($this->registry[$type])) {
			return $this->registry[$type].str_replace(':', "\\", $presenter.'Presenter');
		} else {
			return str_replace(':', '\\', $presenter).'Presenter';
		}
	}

	/**
	 * Formats presenter name from class name.
	 *
	 * @param string presenter class
	 * @return string
	 */
	public function unformatPresenterClass($class)
	{
		$mapper = function ($prefix) use ($class) {
			if (\Nette\StringUtils::startsWith($class, $prefix)) {
				return $prefix;
			}
		};
		$reg = (array) $this->registry->getIterator();
		if (count($prefixes = array_filter($reg, $mapper))) {
			$prefix = current($prefixes);
			return str_replace("\\", ':', substr($class, $class[0] == "\\" ? (strlen($prefix) + 1) : strlen($prefix), -9));
		} else {
			return str_replace("\\", ':', substr($class, $class[0] == "\\" ? 1 : 0, -9));
		}
	}
}
