<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Media\Config;

use Nette\Config\Configurator,
Nette\DI\ContainerBuilder;

/**
 * Media extension
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nella\NetteAddons\Media\Config\Extension
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

		$em = $builder->addDefinition($this->prefix('entityManager'))
			->setFactory($config['entityManager']);

		foreach ($config[self::SERVICES_KEY] as $name => $def) {
			\Nette\Config\Compiler::parseService($builder->addDefinition($this->prefix($name)), $def, FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('fileRepository'))) {
			$builder->addDefinition($this->prefix('fileRepository'))
				->setClass('Nella\Doctrine\Repository')
				->setFactory($this->prefix('@entityManager::getRepository'), array('Nella\Media\Model\FileEntity'))
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('fileDao'))) {
			$builder->addDefinition($this->prefix('fileDao'))
				->setClass('Nella\Media\Model\FileDao', array($em, $this->prefix('@fileRepository')))
				->addSetup('setStorage', array($this->prefix('fileStorage')))
				->setAutowired(FALSE);
		}

		if (isset($config['fileRoute'])) {
			$builder->addDefinition($this->prefix('fileRoute'))
				->setClass('Nella\NetteAddons\Media\Routes\FileRoute', array(
					$config['imageRoute'],
					$builder->getDefinition($this->prefix('fileDao')),
					$builder->getDefinition($this->prefix('filePresenterCallback')),
					'<file>'
				))
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imageRepository'))) {
			$builder->addDefinition($this->prefix('imageRepository'))
				->setClass('Nella\Doctrine\Repository')
				->setFactory($this->prefix('@entityManager::getRepository'), array('Nella\Media\Model\ImageEntity'))
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imageFormatRepository'))) {
			$builder->addDefinition($this->prefix('imageFormatRepository'))
				->setClass('Nella\Doctrine\Repository')
				->setFactory($this->prefix('@entityManager::getRepository'), array('Nella\Media\Model\ImageFormatEntity'))
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imageDao'))) {
			$builder->addDefinition($this->prefix('imageDao'))
				->setClass('Nella\Media\Model\ImageDao', array($em, $this->prefix('@imageRepository')))
				->addSetup('setStorage', array($this->prefix('imageStorage')))
				->addSetup('setCacheStorage', array($this->prefix('imageCacheStorage')))
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imageFormatDao'))) {
			$builder->addDefinition($this->prefix('imageFormatDao'))
				->setClass('Nella\Media\Model\ImageFormatDao', array($em, $this->prefix('@imageFormatRepository')))
				->addSetup('setCacheStorage', array($this->prefix('imageCacheStorage')))
				->setAutowired(FALSE);
		}

		if (isset($config['imageRoute'])) {
			$builder->addDefinition($this->prefix('imageRoute'))
				->setClass('Nella\NetteAddons\Media\Routes\ImageRoute', array(
					$config['imageRoute'],
					$builder->getDefinition($this->prefix('imageDao')),
					$builder->getDefinition($this->prefix('imageFormatDao')),
					$builder->getDefinition($this->prefix('imagePresenterCallback')),
					'<image>'
				))
				->setAutowired(FALSE);
		}

		return parent::loadConfiguration();
	}
}