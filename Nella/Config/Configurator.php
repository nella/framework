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

	public function __construct()
	{
		@header('X-Powered-By: Nette Framework with Nella Framework');
		require_once __DIR__ . '/../shortcuts.php';
		parent::__construct();
	}

	/**
	 * @return array
	 */
	protected function getDefaultParameters()
	{
		$params = parent::getDefaultParameters();
		$trace = debug_backtrace(FALSE);
		$params['appDir'] = isset($trace[2]['file']) ? dirname($trace[2]['file']) : NULL;
		return $params;
	}

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

		$compiler->addExtension('php', new \Nette\Config\Extensions\PhpExtension)
			->addExtension('constants', new \Nette\Config\Extensions\ConstantsExtension)
			->addExtension('nette', $nette)
			->addExtension('doctrine', new Extensions\DoctrineExtension)
			->addExtension('migrations', new \Nella\NetteAddons\Doctrine\Config\MigrationsExtension)
			->addExtension('nella', new Extensions\NellaExtension)
			->addExtension('media', new Extensions\MediaExtension)
			->addExtension('security', new Extensions\SecurityExtension)
			->addExtension('diagnostics', new \Nella\NetteAddons\Diagnostics\Config\Extension)
			->addExtension('model', new Extensions\ModelExtension)
			->addExtension('event', new Extensions\EventExtension($this->getEventManager()));

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

