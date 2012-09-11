<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Console\Config;

use Nette\DI\Container,
	Nette\Config\Compiler,
	Nette\Config\Configurator,
	Symfony\Component\Console\Helper\HelperSet;

/**
 * Console compiler extension
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	const DEFAULT_EXTENSION_NAME = 'console',
		COMMAND_TAG_NAME = 'consoleCommand',
		HELPER_TAG_NAME = 'consoleHelper';

	/** @var array */
	public $defaults = array(
		'name' => \Nette\Framework::NAME,
		'version' => \Nette\Framework::VERSION,
		'catchExceptions' => NULL,
		'route' => 'lazy',
	);

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		if ($config['catchExceptions'] === NULL) {
			$config['catchExceptions'] = $builder->parameters['productionMode'];
		}

		$helperSet = $builder->addDefinition($this->prefix('helperset'))
			->setClass('Symfony\Component\Console\Helper\HelperSet')
			->setFactory(get_called_class().'::createHelperSet', array('@container'));

		$application = $builder->addDefinition($this->prefix('application'))
			->setClass('Symfony\Component\Console\Application')
			->setFactory(get_called_class().'::createApplication', array(
				$config['name'], $config['version'], $helperSet, $config['catchExceptions'], '@container'
			));

		switch ($config['route']) {
			case 'normal':
				$builder->addDefinition($this->prefix('route'))
					->setClass('Nella\Console\Router', array($application))
					->setAutowired(FALSE);
				break;
			case 'lazy':
				$builder->addDefinition($this->prefix('route'))
					->setClass('Nella\Console\LazyRouter', array('@container'))
					->setAutowired(FALSE);
				break;
		}

		if ($builder->hasDefinition('router') && $builder->hasDefinition($this->prefix('route'))) {
			$builder->getDefinition('router')
				->addSetup('offsetSet', array(NULL, $builder->getDefinition($this->prefix('route'))));
		}
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Symfony\Component\Console\Helper\HelperSet
	 */
	public static function createHelperSet(Container $container)
	{
		$helperSet = new \Symfony\Component\Console\Helper\HelperSet;
		$helperSet->set(new \Symfony\Component\Console\Helper\DialogHelper, 'dialog');

		foreach ($container->findByTag(static::HELPER_TAG_NAME) as $name => $value) {
			$helperSet->set($container->getService($name), $value);
		}

		return $helperSet;
	}

	/**
	 * @param string
	 * @param string
	 * @param \Symfony\Component\Console\Helper\HelperSet
	 * @param bool
	 * @param \Nette\DI\Container
	 * @return \Symfony\Component\Console\Application
	 */
	public static function createApplication($name, $version, HelperSet $helperSet, $exceptions = FALSE, Container $container)
	{
		$application = new \Symfony\Component\Console\Application($name, $version);
		$application->setCatchExceptions($exceptions);
		$application->setHelperSet($helperSet);

		foreach ($container->findByTag(static::COMMAND_TAG_NAME) as $name => $value) {
			$application->add($container->getService($name));
		}

		return $application;
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

