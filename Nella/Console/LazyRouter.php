<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Console;

/**
 * Lazy application inicialization router
 *
 * For use Symfony Console
 *
 * @author	Patrik Votoček
 */
class LazyRouter extends Router
{
	/** @var \Nette\Callback */
	private $callback;

	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Nette\DI\Container $container, $serviceName = NULL)
	{
		if (!$serviceName) {
			$class = 'Symfony\Component\Console\Application';
			$lower = ltrim(strtolower($class), '\\');
			if (!isset($container->classes[$lower])) {
				throw new \Nette\DI\MissingServiceException("Service of type $class not found.");
			} elseif ($container->classes[$lower] === FALSE) {
				throw new \Nette\DI\MissingServiceException("Multiple services of type $class found.");
			} else {
				$serviceName = $container->classes[$lower];
			}
		}

		if (!$container->hasService($serviceName)) {
			throw new \Nette\DI\MissingServiceException("Service '$serviceName' not found.");
		}

		$this->callback = callback(function () use ($container, $serviceName) {
			$console = $container->getService($serviceName);
			$console->run();
		});
	}
}

