<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Config\Extensions;

use Nette\Config\Configurator,
	Nette\DI\ContainerBuilder;

/**
 * Media extension
 *
 * @author	Patrik Votoček
 */
class MediaExtension extends \Nella\Media\Config\Extension
{
	/** @var array */
	public $defaults = array(
		'imagePath' => '%wwwDir%/images',
		'fileStorageDir' => '%appDir%/storage/files',
		'imageStorageDir' => '%appDir%/storage/images',
		'formats' => array(),
		self::SERVICES_KEY => array(),
	);

	/**
	 * Processes configuration data
	 *
	 * @throws \Nette\InvalidStateException
	 */
	public function loadConfiguration()
	{
		if (!$this->getConfig()) {
			return;
		}

		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		if (!isset($config['entityManager'])) {
			throw new \Nette\InvalidStateException('Model entity manager does not set');
		}

		$builder->addDefinition($this->prefix('entityManager'))
			->setFactory($config['entityManager'])
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('listener'))
			->setClass('Nella\Media\Doctrine\Listener')
			->addTag('doctrineListener')
			->setAutowired(FALSE);

		parent::loadConfiguration();
	}

	/**
	 * @param string
	 * @param string|NULL
	 */
	protected function processFile($storageDir, $routeMask = NULL)
	{
		parent::processFile($storageDir, $routeMask);

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('fileRepository'))
			->setClass('Nella\Doctrine\Repository')
			->setFactory($this->prefix('@entityManager::getRepository'), array('Nella\Media\Doctrine\FileEntity'))
			->setAutowired(FALSE);

		if ($builder->hasDefinition($this->prefix('fileDao'))) {
			$builder->removeDefinition($this->prefix('fileDao'));
		}
		$builder->addDefinition($this->prefix('fileDao'))
			->setClass('Nella\Media\Doctrine\FileDao', array(
				$this->prefix('@entityManager'), $this->prefix('@fileRepository')
			))
			->addSetup('setStorage', array($this->prefix('@fileStorage')))
			->setAutowired(FALSE);

		if ($routeMask) {
			$builder->getDefinition($this->prefix('fileRoute'))
				->setClass('Nella\Media\Routes\FileRoute', array(
					$routeMask, $this->prefix('@fileDao'), $this->prefix('@filePresenterCallback'), '<file>'
				));
		}
	}

	/**
	 * @param string
	 * @param string
	 * @param array
	 * @param string|NULL
	 */
	protected function processImage($storageDir, $path, array $formats, $routeMask = NULL)
	{
		parent::processImage($storageDir, $path, $formats, $routeMask);

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('imageRepository'))
			->setClass('Nella\Doctrine\Repository')
			->setFactory($this->prefix('@entityManager::getRepository'), array('Nella\Media\Doctrine\ImageEntity'))
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('imageFormatRepository'))
			->setClass('Nella\Doctrine\Repository')
			->setFactory($this->prefix('@entityManager::getRepository'), array('Nella\Media\Doctrine\ImageFormatEntity'))
			->setAutowired(FALSE);

		if ($builder->hasDefinition($this->prefix('imageDao'))) {
			$builder->removeDefinition($this->prefix('imageDao'));
		}
		$builder->addDefinition($this->prefix('imageDao'))
			->setClass('Nella\Media\Doctrine\ImageDao', array(
				$this->prefix('@entityManager'), $this->prefix('@imageRepository')
			))
			->addSetup('setStorage', array($this->prefix('@imageStorage')))
			->addSetup('setCacheStorage', array($this->prefix('@imageCacheStorage')))
			->setAutowired(FALSE);

		if ($builder->hasDefinition($this->prefix('imageFormatDao'))) {
			$builder->removeDefinition($this->prefix('imageFormatDao'));
		}
		$builder->addDefinition($this->prefix('imageFormatDao'))
			->setClass('Nella\Media\Doctrine\ImageFormatDao', array(
				$this->prefix('@entityManager'), $this->prefix('@imageFormatRepository')
			))
			->addSetup('setCacheStorage', array($this->prefix('@imageCacheStorage')))
			->setAutowired(FALSE);

		if ($routeMask) {
			$builder->getDefinition($this->prefix('imageRoute'))
				->setClass('Nella\Media\Routes\ImageRoute', array(
					$routeMask,
					$this->prefix('@imageDao'),
					$this->prefix('@imageFormatDao'),
					$this->prefix('@imagePresenterCallback'),
					'<image>'
				));
		}
	}
}

