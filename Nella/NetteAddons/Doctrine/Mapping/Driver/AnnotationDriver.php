<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Doctrine\Mapping\Driver;

/**
 * Annotation driver with ignore dirs support
 *
 * @author  Patrik Votoček
 * @author	Pavel Kučera
 */
class AnnotationDriver extends \Doctrine\ORM\Mapping\Driver\AnnotationDriver
{
	/** @var array */
	private $ignoredDirs = array();

	/**
	 * @param string
	 * @return AnnotationDriver
	 */
	public function addIgnoredDir($dir)
	{
		$this->ignoredDirs[] = realpath($dir);
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllClassNames()
	{
		if ($this->_classNames !== null) {
			return $this->_classNames;
		}

		if (!$this->_paths) {
			throw \Doctrine\ORM\Mapping\MappingException::pathRequired();
		}

		$classes = array();
		$includedFiles = array();

		foreach ($this->_paths as $path) {
			if (!is_dir($path)) {
				throw \Doctrine\ORM\Mapping\MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
			}

			$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);

			foreach ($iterator as $file) {
				if (($fileName = $file->getBasename($this->_fileExtension)) == $file->getBasename()) {
					continue;
				}

				$sourceFile = realpath($file->getPathName());
				foreach ($this->ignoredDirs as $dir) {
					if (strpos($sourceFile, $dir) !== FALSE) {
						continue 2;
					}
				}
				\Nette\Utils\LimitedScope::load($sourceFile, TRUE);
				$includedFiles[] = $sourceFile;
			}
		}

		$declared = get_declared_classes();

		foreach ($declared as $className) {
			$rc = new \ReflectionClass($className);
			$sourceFile = $rc->getFileName();
			if (in_array($sourceFile, $includedFiles) && ! $this->isTransient($className)) {
				$classes[] = $className;
			}
		}

		$this->_classNames = $classes;

		return $classes;
	}
}