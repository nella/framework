<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media\Config;

use Nette\Config\Configurator,
	Nette\Config\Compiler,
	Nette\DI\ContainerBuilder;

/**
 * Doctrine Nella Framework services.
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	const DEFAULT_EXTENSION_NAME = 'media',
		SERVICES_KEY = 'services';

	/** @var array */
	public $defaults = array(
		'imagePath' => '%wwwDir%/images',
		'fileStorageDir' => '%appDir%/storage/files',
		'imageStorageDir' => '%appDir%/storage/images',
		'formats' => array(
			'default' => array(
				'width' => 800,
				'height' => 600,
			),
			'thumbnail' => array(
				'width' => 100,
				'height' => 100,
				'crop' => TRUE,
			)
		),
		self::SERVICES_KEY => array(),
	);

	/**
	 * Processes configuration data
	 *
	 * @throws \Nette\InvalidStateException
	 */
	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$cache = $builder->addDefinition($this->prefix('cache'))
			->setClass('Nette\Caching\IStorage')
			->setFactory('@cacheStorage');

		// Basic file
		$fileStorage = $builder->addDefinition($this->prefix('fileStorage'))
			->setClass('Nella\Media\Storages\File', array($config['fileStorageDir']))
			->setAutowired(FALSE);

		$fileCallback = $builder->addDefinition($this->prefix('filePresenterCallback'))
			->setClass('Nella\Media\Callbacks\FilePresenterCallback', array($fileStorage));

		// Basic image
		$imageStorage = $builder->addDefinition($this->prefix('imageStorage'))
			->setClass('Nella\Media\Storages\File', array($config['imageStorageDir']))
			->setAutowired(FALSE);

		$imageCacheStorage = $builder->addDefinition($this->prefix('imageCacheStorage'))
			->setClass('Nella\Media\ImageCacheStorages\File', array($config['imagePath'], $cache));

		$imageCallback = $builder->addDefinition($this->prefix('imagePresenterCallback'))
			->setClass('Nella\Media\Callbacks\ImagePresenterCallback', array($imageStorage, $imageCacheStorage));

		if (!isset($config['entityManager'])) {
			$em = $builder->addDefinition($this->prefix('entityManager'))
				->setFactory($config['entityManager'])
				->setAutowired(FALSE);

			$listener = $builder->addDefinition($this->prefix('listener'))
				->setClass('Nella\Media\Doctrine\Listener')
				->setAutowired(FALSE);

			if (class_exists('Nella\Doctrine\Config\Extension')) {
				$listener->addTag(\Nella\Doctrine\Config\Extension::EVENT_TAG_NAME);
			} else {
				$em->addSetup(get_called_class().'::setupListener', array('@self', $listener));
			}

			$fileFacade = $this->processFileDoctrine('entityManager', $fileStorage);
			$imageFacade = $this->processImageDoctrine('entityManager', $imageStorage, $imageCacheStorage);
			$imageFormatFacade = $this->processImageFormatDoctrine('entityManager', $imageCacheStorage);
		} else {
			$fileFacade = $builder->addDefinition($this->prefix('fileFacade'))
				->setClass('Nella\Media\Model\FileFacade')
				->setAutowired(FALSE);

			$imageFacade = $builder->addDefinition($this->prefix('imageFacade'))
				->setClass('Nella\Media\Model\ImageFacade')
				->setAutowired(FALSE);

			$imageFormatFacade = $builder->addDefinition($this->prefix('imageFormatFacade'))
				->setClass('Nella\Media\Model\ImageFormatFacade', array($config['formats']))
				->setAutowired(FALSE);
		}

		foreach ($config[self::SERVICES_KEY] as $name => $def) {
			if ($this->hasDefinition($this->prefix($name))) {
				$this->removeDefinition($this->prefix($name));
			}

			\Nette\Config\Compiler::parseService(
				$builder->addDefinition($this->prefix($name)), $def, FALSE
			);
		}

		if ($builder->hasDefinition('nette.latte')) {
			$builder->getDefinition('nette.latte')
				->addSetup('Nella\Media\Latte\MediaMacros::factory', array('@self'));
		}

		if (isset($config['fileRoute'])) {
			$builder->addDefinition($this->prefix('fileRoute'))
				->setClass('Nella\Media\Routes\FileRoute', array($config['fileRoute'], $fileFacade, $fileCallback))
				->setAutowired(FALSE);
		}

		if (isset($config['imageRoute'])) {
			$builder->addDefinition($this->prefix('imageRoute'))
				->setClass('Nella\Media\Routes\ImageRoute', array(
					$config['imageRoute'], $imageFacade, $imageFormatFacade, $imageCallback
				))
				->setAutowired(FALSE);
		}

		$this->registerRoutes();
	}

	/**
	 * @param string
	 * @param \Nette\DI\ServiceDefinition|string
	 * @return \Nette\DI\ServiceDefinition|string
	 */
	protected function processFileDoctrine($entitManager, $storage)
	{
		$builder = $this->getContainerBuilder();

		$repository = $builder->addDefinition($this->prefix('fileRepository'))
			->setClass('Nella\Doctrine\Repository')
			->setFactory($this->prefix("$entityManager::getRepository"), array('Nella\Media\Doctrine\FileEntity'))
			->setAutowired(FALSE);

		return $builder->addDefinition($this->prefix('fileFacade'))
			->setClass('Nella\Media\Doctrine\FileFacade', array($entitManager, $repository))
			->addSetup('setStorage', array($storage))
			->setAutowired(FALSE);
	}

	/**
	 * @param string
	 * @param \Nette\DI\ServiceDefinition|string
	 * @param \Nette\DI\ServiceDefinition|string
	 * @return \Nette\DI\ServiceDefinition|string
	 */
	protected function processImageDoctrine($entitManager, $storage, $cacheStorage)
	{
		$builder = $this->getContainerBuilder();

		$repository = $builder->addDefinition($this->prefix('imageRepository'))
			->setClass('Nella\Doctrine\Repository')
			->setFactory($this->prefix("$entityManager::getRepository"), array('Nella\Media\Doctrine\ImageEntity'))
			->setAutowired(FALSE);

		return $builder->addDefinition($this->prefix('imageFacade'))
			->setClass('Nella\Media\Doctrine\ImageFacade', array($entitManager, $repository))
			->addSetup('setStorage', array($storage))
			->addSetup('setCacheStorage', array($cacheStorage))
			->setAutowired(FALSE);

	}

	/**
	 * @param string
	 * @param \Nette\DI\ServiceDefinition|string
	 * @return \Nette\DI\ServiceDefinition|string
	 */
	protected function processImageFormatDoctrine($entitManager, $cacheStorage)
	{
		$builder = $this->getContainerBuilder();

		$repository = $builder->addDefinition($this->prefix('imageFormatRepository'))
			->setClass('Nella\Doctrine\Repository')
			->setFactory($this->prefix("$entityManager::getRepository"), array('Nella\Media\Doctrine\ImageFormatEntity'))
			->setAutowired(FALSE);

		return $builder->addDefinition($this->prefix('imageFormatFacade'))
			->setClass('Nella\Media\Doctrine\ImageFormatFacade', array($entitManager, $repository))
			->addSetup('setCacheStorage', array($cacheStorage))
			->setAutowired(FALSE);
	}

	protected function registerRoutes()
	{
		$builder = $this->getContainerBuilder();

		if ($builder->hasDefinition('router')) {
			if ($builder->hasDefinition($this->prefix('fileRoute'))) {
				$builder->getDefinition('router')
					->addSetup('offsetSet', array(NULL, $this->prefix('@fileRoute')));
			}
			if ($builder->hasDefinition($this->prefix('imageRoute'))) {
				$builder->getDefinition('router')
					->addSetup('offsetSet', array(NULL, $this->prefix('@imageRoute')));
			}
		}
	}

	/**
	 * @param \Doctrine\ORM\EntityManager
	 * @param \Nella\Media\Doctrine\Listener
	 */
	public static function setupListener(\Doctrine\ORM\EntityManager $entityManager, \Nella\Media\Doctrine\Listener $listener)
	{
		$evm = $entityManager->getEventManager();
		$evm->addEventSubscriber($listener);
	}

	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = self::DEFAULT_EXTENSION_NAME)
	{
		$class = get_called_class();
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) use ($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}

