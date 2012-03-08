<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\NetteAddons\Media\Config;

use Nette\Config\Configurator,
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

		foreach ($config[self::SERVICES_KEY] as $name => $def) {
			\Nette\Config\Compiler::parseService($builder->addDefinition($this->prefix($name)), $def, FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('fileStorage'))) {
			$builder->addDefinition($this->prefix('fileStorage'))
				->setClass('Nella\NetteAddons\Media\Storages\File', array($config['fileStorageDir']))
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('filePresenterCallback'))) {
			$builder->addDefinition($this->prefix('filePresenterCallback'))
				->setClass('Nella\NetteAddons\Media\Callbacks\FilePresenterCallback', array(
					$builder->getDefinition($this->prefix('fileStorage'))
				));
		}

		if (!$builder->hasDefinition($this->prefix('fileDao'))) {
			$builder->addDefinition($this->prefix('fileDao'))
				->setClass('Nella\NetteAddons\Media\Model\FileDao')
				->setAutowired(FALSE);
		}

		if (isset($config['fileRoute'])) {
			$builder->addDefinition($this->prefix('fileRoute'))
				->setClass('Nella\NetteAddons\Media\Routes\FileRoute', array(
					$config['imageRoute'],
					$builder->getDefinition($this->prefix('fileDao')), $builder->getDefinition($this->prefix('filePresenterCallback'))
				))
				//->addSetup('@router::offsetSet(?, ?)', array(NULL, '@self'))
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imageStorage'))) {
			$builder->addDefinition($this->prefix('imageStorage'))
				->setClass('Nella\NetteAddons\Media\Storages\File', array($config['imageStorageDir']))
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imageCacheStorage'))) {
			$builder->addDefinition($this->prefix('imageCacheStorage'))
				->setClass('Nella\NetteAddons\Media\ImageCacheStorages\File', array($config['imagePath'], '@cacheStorage'));
		}

		if (!$builder->hasDefinition($this->prefix('imagePresenterCallback'))) {
			$builder->addDefinition($this->prefix('imagePresenterCallback'))
				->setClass('Nella\NetteAddons\Media\Callbacks\ImagePresenterCallback', array(
					$builder->getDefinition($this->prefix('imageStorage')), $builder->getDefinition($this->prefix('imageCacheStorage'))
				));
		}

		if (!$builder->hasDefinition($this->prefix('imageDao'))) {
			$builder->addDefinition($this->prefix('imageDao'))
				->setClass('Nella\NetteAddons\Media\Model\ImageDao')
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imageFormatDao'))) {
			$builder->addDefinition($this->prefix('imageFormatDao'))
				->setClass('Nella\NetteAddons\Media\Model\ImageFormatDao', array($config['formats']))
				->setAutowired(FALSE);
		}

		if (isset($config['imageRoute'])) {
			$builder->addDefinition($this->prefix('imageRoute'))
				->setClass('Nella\NetteAddons\Media\Routes\ImageRoute', array(
					$config['imageRoute'],
					$builder->getDefinition($this->prefix('imageDao')), $builder->getDefinition($this->prefix('imageFormatDao')),
					$builder->getDefinition($this->prefix('imagePresenterCallback'))
				))
				//->addSetup('@router::offsetSet(?, ?)', array(NULL, '@self'))
				->setAutowired(FALSE);
		}

		if ($builder->hasDefinition('nette.latte')) {
			$builder->getDefinition('nette.latte')
				->addSetup('Nella\NetteAddons\Media\Latte\MediaMacros::factory', array('@self'));
		}

		if ($builder->hasDefinition('router')) {
			if ($builder->hasDefinition($this->prefix('fileRoute'))) {
				$builder->getDefinition('router')
					->addSetup('offsetSet', array(NULL, $builder->getDefinition($this->prefix('fileRoute'))));
			}
			if ($builder->hasDefinition($this->prefix('imageRoute'))) {
				$builder->getDefinition('router')
					->addSetup('offsetSet', array(NULL, $builder->getDefinition($this->prefix('imageRoute'))));
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
		$configurator->onCompile[] = function(Configurator $configurator, \Nette\Config\Compiler $compiler) use($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}