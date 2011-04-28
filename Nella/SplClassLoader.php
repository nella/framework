<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella;

use Nette\Utils\Strings;

/**
 * PSR-0 class & interface auto loader.
 *
 * @author	Patrik Votoček
 */
class SplClassLoader extends \Nette\Loaders\AutoLoader
{
	/** @var NellaLoader */
	private static $instance;
	/** @var array */
	private $map;

	/**
	 * @param array
	 */
	protected function __construct(array $map = array('Nella' => NELLA_FRAMEWORK_DIR))
	{
		$this->map = $map;
	}

	/**
	 * Returns singleton instance with lazy instantiation.
	 * @param array
	 * @return SplClassLoader
	 */
	public static function getInstance(array $map = array('Nella' => NELLA_FRAMEWORK_DIR))
	{
		if (self::$instance === NULL) {
			self::$instance = new self($map);
		}
		return self::$instance;
	}

	/**
	 * Handles autoloading of classes or interfaces.
	 * @param  string
	 * @return void
	 */
	public function tryLoad($type)
	{
		$mapper = function ($namespace) use ($type) { // find namespace in map
			return Strings::startsWith(strtolower($type), strtolower($namespace)) ? $namespace : NULL;
		};
		$namespace = array_filter(array_keys($this->map), $mapper);
		sort($namespace);
		if (count($namespace)) { // is in map?
			$namespace = end($namespace);
			$type = substr($type, Strings::length($namespace) + (Strings::endsWith($namespace, '\\') ? 0 : 1)); // remove namespace
			$path = $this->map[$namespace] . "/"; // map dir
			$path .= str_replace('\\', DIRECTORY_SEPARATOR, $type); // class to file in map
			$path .= ".php";

			if (file_exists($path)) {
				\Nette\Utils\LimitedScope::load($path);
			}
		}
	}
}
