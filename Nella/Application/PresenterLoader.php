<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Application;

/**
 * Nella presenter loader
 *
 * @author	Patrik Votoček
 */
class PresenterLoader extends \Nette\Application\PresenterLoader
{
	/** @var array */
	public $prefixies = array(
		'app' => "App\\", 
		'framework' => "Nella\\", 
	);
	
	/**
	 * Format presenter class with prefixies
	 * 
	 * @param string
	 * @return string
	 * @throws \Nette\Application\InvalidPresenterException
	 */
	private function formatPresenterClasses($name)
	{
		$class = NULL;
		foreach (array_keys($this->prefixies) as $key) {
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
	 * @throws Nette\Application\InvalidPresenterException
	 */
	public function getPresenterClass(& $name)
	{
		if (!is_string($name) || !preg_match("#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*$#", $name)) {
			throw new \Nette\Application\InvalidPresenterException("Presenter name must be alphanumeric string, '$name' is invalid.");
		}

		$class = $this->formatPresenterClasses($name);
		$reflection = \Nette\Reflection\ClassReflection::from($class);
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
		if (isset($this->prefixies[$type])) {
			return $this->prefixies[$type].str_replace(':', "\\", $presenter.'Presenter');
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
			if (\Nette\String::startsWith($class, $prefix)) {
				return $prefix;
			}
		};
		if (count($prefixies = array_filter($this->prefixies, $mapper))) {
			$prefix = current($prefixies);
			return str_replace("\\", ':', substr($class, $class[0] == "\\" ? (strlen($prefix) + 1) : strlen($prefix), -9));
		} else {
			return str_replace("\\", ':', substr($class, $class[0] == "\\" ? 1 : 0, -9));
		}
	}

	/**
	 * Presenter loader factory
	 *
	 * @return PresenterLoader
	 */
	public static function createPresenterLoader()
	{
		return new static(APP_DIR);
	}
}
