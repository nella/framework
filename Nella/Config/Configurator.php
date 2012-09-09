<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Config;

/**
 * Initial system DI container generator.
 *
 * @author	Patrik Votoček
 *
 * @property-read \Nella\SplClassLoader $splClassLoader
 */
class Configurator extends \Nette\Config\Configurator
{
	/** @var \Nella\Event\IEventDispatcher */
	private $eventManager;

	/**
	 * @return \Nella\Event\IEventDispatcher
	 */
	public function getEventManager()
	{
		if (!$this->eventManager) {
			$this->eventManager = new \Nella\Event\EventDispatcher;
		}
		return $this->eventManager;
	}

	/**
	 * @param \Nella\Event\IEventDispatcher
	 * @return Configurator
	 * @throws \Nette\InvalidStateException
	 */
	public function setEventManager(\Nella\Event\IEventDispatcher $eventManager)
	{
		if ($this->eventManager) {
			throw new \Nette\InvalidStateException('Event manager already initialized');
		}
		$this->eventManager = $eventManager;
		return $this;
	}

	/**
	 * @return \Nella\SplClassLoader
	 */
	public function getSplClassLoader()
	{
		return \Nella\SplClassLoader::getInstance();
	}

	/**
	 * @param string
	 * @param string|bool
	 * @return \Nette\Config\Configurator
	 */
	public function addConfig($file, $section = self::NONE)
	{
		return parent::addConfig($file, $section);
	}

	/**
	 * @param string
	 * @param string|bool
	 * @return \Nette\Config\Configurator
	 */
	public function addConfigIfExist($file, $section = self::NONE)
	{
		if (!file_exists($file)) {
			return $this;
		}

		return $this->addConfig($file, $section);
	}

	/**
	 * @return Compiler
	 */
	protected function createCompiler()
	{
		$compiler = new \Nette\Config\Compiler;

		$nette = new \Nette\Config\Extensions\NetteExtension;
		$nette->defaults['container']['debugger'] = TRUE;

		$console = new \Nella\Console\Config\Extension;
		$console->defaults['name'] = \Nella\Framework::NAME;
		$console->defaults['version'] = \Nella\Framework::VERSION;
		$doctrine = new \Nella\Doctrine\Config\Extension;
		$doctrine->defaults['entityDirs'][] = __DIR__ . '/../';
		$doctrine->defaults['console'] = TRUE;
		$doctrine->defaults['repositoryClass'] = 'Nella\Model\Repository';
		$migrations = new \Nella\Doctrine\Config\MigrationsExtension;
		$migrations->defaultName = \Nella\Framework::NAME . ' DB Migrations';
		$media = new \Nella\Media\Config\Extension;
		$media->defaults['entityManager'] = '@' . \Nella\Doctrine\Config\Extension::DEFAULT_EXTENSION_NAME . '.entityManager';

		$compiler->addExtension('php', new \Nette\Config\Extensions\PhpExtension)
			->addExtension('constants', new \Nette\Config\Extensions\ConstantsExtension)
			->addExtension('nette', $nette)
			->addExtension(\Nella\Doctrine\Config\Extension::DEFAULT_EXTENSION_NAME, $doctrine)
			->addExtension(\Nella\Doctrine\Config\MigrationsExtension::DEFAULT_EXTENSION_NAME, $migrations)
			->addExtension('nella', new Extensions\NellaExtension)
			->addExtension(\Nella\Media\Config\Extension::DEFAULT_EXTENSION_NAME, $media)
			->addExtension('security', new Extensions\SecurityExtension)
			->addExtension('diagnostics', new \Nella\Diagnostics\Config\Extension)
			->addExtension('model', new Extensions\ModelExtension)
			->addExtension('event', new Extensions\EventExtension($this->getEventManager()))
			->addExtension(\Nella\Console\Config\Extension::DEFAULT_EXTENSION_NAME, $console);

		$this->eventManager->dispatchEvent(\Nella\Events::CREATE_COMPILER, new \Nella\Event\Args\Compiler($compiler));

		return $compiler;
	}

	/**
	 * @return \SystemContainer
	 */
	public function createContainer()
	{
		$container = parent::createContainer();
		$container->addService('event.manager', $this->getEventManager());
		return $container;
	}
}

