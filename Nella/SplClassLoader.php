<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella;

use Nette\Utils\Strings;

/**
 * PSR-0 class & interface auto loader.
 *
 * @author	Patrik Votoček
 */
final class SplClassLoader extends \Nette\Loaders\AutoLoader
{
	/** @var SplClassLoader */
	private static $instance;
	/** @var array */
	private $map;

	/**
	 * @param array
	 */
	protected function __construct(array $map = array('Nella' => __DIR__))
	{
		$this->map = $map;
	}

	/**
	 * @param string
	 * @param string
	 */
	public function addNamespaceAlias($namespace, $dir)
	{
		$this->map[$namespace] = $dir;
		return $this;
	}

	/**
	 * Returns singleton instance with lazy instantiation.
	 * @param array
	 * @return SplClassLoader
	 */
	public static function getInstance(array $map = array('Nella' => __DIR__))
	{
		if (static::$instance === NULL) {
			static::$instance = new self($map);
		}
		return static::$instance;
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
