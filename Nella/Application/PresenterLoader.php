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
			throw new \Nette\Application\InvalidPresenterException(
				"Presenter name must be alphanumeric string, '$name' is invalid."
			);
		}

		$appClass = $class = $this->formatPresenterClass($name);

		if (!class_exists($class)) {
			$class = $this->formatPresenterClass($name, "lib");
			if (!class_exists($class)) {
				throw new \Nette\Application\InvalidPresenterException(
					"Cannot load presenter '$name', class '$appClass' was not found."
				);
			}
		}

		$reflection = \Nette\Reflection\ClassReflection::from($class);
		$class = $reflection->getName();

		if (!$reflection->implementsInterface('Nette\Application\IPresenter')) {
			throw new \Nette\Application\InvalidPresenterException(
				"Cannot load presenter '$name', class '$class' is not Nette\\Application\\IPresenter implementor."
			);
		}
		if ($reflection->isAbstract()) {
			throw new \Nette\Application\InvalidPresenterException(
				"Cannot load presenter '$name', class '$class' is abstract."
			);
		}

		// canonicalize presenter name
		$realName = $this->unformatPresenterClass($class);
		if ($name !== $realName) {
			if ($this->caseSensitive) {
				throw new \Nette\Application\InvalidPresenterException(
					"Cannot load presenter '$name', case mismatch. Real name is '$realName'."
				);
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
	public function formatPresenterClass($presenter, $type = NULL)
	{
		if ($type == "lib") {
			return 'Nella\\'.str_replace(':', "\\", $presenter.'Presenter');
		} else {
			return 'App\\' . str_replace(':', '\\', $presenter).'Presenter';
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
		if (strpos($class, 'Nella') !== FALSE) {
			return str_replace("\\", ':', substr($class, $class[0] == "\\" ? 7 : 6, -9));
		} else {
			return str_replace("\\", ':', substr($class, $class[0] == "\\" ? 5 : 4, -9));
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
