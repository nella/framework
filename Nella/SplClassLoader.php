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
	private $map = array();

	/**
	 * @param array
	 */
	protected function __construct(array $map = array('Nella' => __DIR__))
	{
		foreach ($map as $namespace => $path) {
			$this->addNamespaceAlias($namespace, $path);
		}
	}

	/**
	 * Returns singleton instance with lazy instantiation
	 *
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
	 * @param string
	 * @param string
	 */
	public function addNamespaceAlias($namespace, $path)
	{
		$this->map[$namespace] = realpath($path);
		return $this;
	}

	/**
	 * Handles autoloading of classes or interfaces
	 *
	 * @param  string
	 */
	public function tryLoad($class)
	{
		$path = $this->formatFilePath($class);
		if ($path !== NULL && file_exists($path)) {
			\Nette\Utils\LimitedScope::load($path);
		}
	}

	/**
	* @param string
	* @return string|NULL
	*/
	protected function formatFilePath($class)
	{
		if (Strings::startsWith($class, '\\')) {
			$class = Strings::substring($class, 1);
		}

		foreach ($this->map as $prefix => $dir) {
			if (empty($prefix) || Strings::startsWith($class, $prefix . '\\')) {
				$file = $class . '.php'; // non namespace class
				if (Strings::contains($class, '\\')) {
					$part = Strings::substring($class, Strings::length($prefix)); // remove $prefix from full class
					$file = str_replace('\\', DIRECTORY_SEPARATOR, $part) . '.php'; // convert part of full class to relative path
				}

				$path = $dir . DIRECTORY_SEPARATOR . $file;
				if (file_exists($path)) {
					return $path;
				}
			}
		}
	}
}
