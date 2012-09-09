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
	const SERVICES_KEY = 'services';

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

		$this->processFile($config['fileStorageDir'], isset($config['fileRoute']) ? $config['fileRoute'] : NULL);
		$this->processImage(
			$config['imageStorageDir'], $config['imagePath'], $config['formats'],
			isset($config['imageRoute']) ? $config['imageRoute'] : NULL
		);

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

		$this->registerRoutes();
	}

	/**
	 * @param string
	 * @param string|NULL
	 */
	protected function processFile($storageDir, $routeMask = NULL)
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('fileStorage'))
			->setClass('Nella\Media\Storages\File', array($storageDir))
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('filePresenterCallback'))
			->setClass('Nella\Media\Callbacks\FilePresenterCallback', array(
				$this->prefix('@fileStorage')
			));

		$builder->addDefinition($this->prefix('fileFacade'))
			->setClass('Nella\Media\Model\FileFacade')
			->setAutowired(FALSE);

		if ($routeMask) {
			$builder->addDefinition($this->prefix('fileRoute'))
				->setClass('Nella\Media\Routes\FileRoute', array(
					$routeMask, $this->prefix('@fileFacade'), $this->prefix('@filePresenterCallback')
				))
				->setAutowired(FALSE);
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
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('imageStorage'))
			->setClass('Nella\Media\Storages\File', array($storageDir))
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('imageCacheStorage'))
			->setClass('Nella\Media\ImageCacheStorages\File', array($path, '@cacheStorage'));

		$builder->addDefinition($this->prefix('imagePresenterCallback'))
			->setClass('Nella\Media\Callbacks\ImagePresenterCallback', array(
				$this->prefix('@imageStorage'), $this->prefix('@imageCacheStorage')
			));

		$builder->addDefinition($this->prefix('imageFacade'))
			->setClass('Nella\Media\Model\ImageFacade')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('imageFormatFacade'))
			->setClass('Nella\Media\Model\ImageFormatFacade', array($formats))
			->setAutowired(FALSE);

		if ($routeMask) {
			$builder->addDefinition($this->prefix('imageRoute'))
				->setClass('Nella\Media\Routes\ImageRoute', array(
					$routeMask, $this->prefix('@imageFacade'), $this->prefix('@imageFormatFacade'),
					$this->prefix('@imagePresenterCallback')
				))
				->setAutowired(FALSE);
		}
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
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = 'media')
	{
		$class = get_called_class();
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) use ($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}

