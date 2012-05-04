<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Config\Extensions;

use Nette\Application\Application,
	Nella\Event\Args;

/**
 * Event config compiler extension
 *
 * @author    Patrik Votoček
 */
class EventExtension extends \Nette\Config\CompilerExtension
{
	/** @var \Nella\Event\IEventDispatcher */
	private $eventManager;

	/**
	 * @param \Nella\Event\IEventDispatcher
	 */
	public function __construct(\Nella\Event\IEventDispatcher $eventManager)
	{
		$this->eventManager = $eventManager;
	}

	public function beforeCompile()
	{
		$this->eventManager->dispatchEvent(
			\Nella\Events::BEFORE_CONTAINER_COMPILE,
			new \Nella\Event\Args\CompilerBefore($this->compiler, $this->getContainerBuilder())
		);
	}

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('manager'))
			->setClass(get_class($this->eventManager));
		$builder->getDefinition('application')
			->addSetup(get_called_class() . '::setupApplication', array('@self', $this->prefix('@manager')));
	}

	public function afterCompile(\Nette\Utils\PhpGenerator\ClassType $class)
	{
		$this->eventManager->dispatchEvent(
			\Nella\Events::BEFORE_CONTAINER_COMPILE, new \Nella\Event\Args\CompilerAfter($this->compiler, $class)
		);
	}

	/**
	 * @param \Nette\Application\Application
	 * @param \Nella\Event\IEventDispatcher
	 */
	public static function setupApplication(Application $application, \Nella\Event\IEventDispatcher $eventManager)
	{
		$application->onStartup[] = function(Application $application) use($eventManager) {
			$eventManager->dispatchEvent(
				\Nella\Events::APPLICATION_ERROR, new Args\Application($application)
			);
		};

		$application->onError[] = function(Application $application, \Exception $exception) use($eventManager) {
			$eventManager->dispatchEvent(
				\Nella\Events::APPLICATION_ERROR, new Args\ApplicationError($application, $exception)
			);
		};

		$application->onRequest[] = function(Application $application, \Nette\Application\Request $request) use($eventManager) {
			$eventManager->dispatchEvent(
				\Nella\Events::APPLICATION_ERROR, new Args\ApplicationRequest($application, $request)
			);
		};

		$application->onResponse[] = function(Application $application, \Nette\Application\IResponse $response) use($eventManager) {
			$eventManager->dispatchEvent(
				\Nella\Events::APPLICATION_ERROR, new Args\ApplicationResponse($application, $response)
			);
		};

		$application->onShutdown[] = function(Application $application, \Exception $exception = NULL) use($eventManager) {
			$eventManager->dispatchEvent(
				\Nella\Events::APPLICATION_ERROR, new Args\ApplicationShutdown($application, $exception)
			);
		};
	}
}
