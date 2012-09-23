<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Event\Config;

use Nette\Config\Compiler,
	Nette\Config\Configurator,
	Nette\Application\Application,
	Nella\Event\Args,
	Nette\Application\Request,
	Nette\Application\IResponse,
	Nella\Events,
	Nella\Event\IEventDispatcher;

/**
 * Event config compiler extension
 *
 * @author    Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	const DEFAULT_EXTENSION_NAME = 'event';

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$evm = $builder->addDefinition($this->prefix('manager'))
			->setClass('Nella\Event\EventDispatcher');
		$builder->getDefinition('application')
			->addSetup(get_called_class() . '::setupApplication', array('@self', $evm));
	}

	/**
	 * @param \Nette\Application\Application
	 * @param \Nella\Event\IEventDispatcher
	 */
	public static function setupApplication(Application $application, IEventDispatcher $evm)
	{
		$application->onStartup[] = function (Application $application) use ($evm) {
			$evm->dispatchEvent(Events::APPLICATION_STARTUP, new Args\Application($application));
		};

		$application->onError[] = function (Application $application, \Exception $exception) use ($evm) {
			$evm->dispatchEvent(Events::APPLICATION_ERROR, new Args\ApplicationError($application, $exception));
		};

		$application->onRequest[] = function (Application $application, Request $request) use ($evm) {
			$evm->dispatchEvent(Events::APPLICATION_REQUEST, new Args\ApplicationRequest($application, $request));
		};

		$application->onResponse[] = function (Application $application, IResponse $response) use ($evm) {
			$evm->dispatchEvent(Events::APPLICATION_RESPONSE, new Args\ApplicationResponse($application, $response));
		};

		$application->onShutdown[] = function (Application $application, \Exception $exception = NULL) use ($evm) {
			$evm->dispatchEvent(Events::APPLICATION_SHUTDOWN, new Args\ApplicationShutdown($application, $exception));
		};
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

