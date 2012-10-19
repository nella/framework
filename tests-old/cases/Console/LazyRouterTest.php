<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Console;

use	Nette\DI\Container,
	Symfony\Component\Console\Application,
	Nella\Console\LazyRouter;

class LazyRouterTest extends \Nella\Testing\TestCase
{
	/**
	 * @expectedException Nette\DI\MissingServiceException
	 */
	public function testMissingServiceByName()
	{
		$container = new Container;
		new LazyRouter($container, 'console.application');
	}

	/**
	 * @expectedException Nette\DI\MissingServiceException
	 */
	public function testMissingServiceByType()
	{
		$container = new Container;
		new LazyRouter($container);
	}

	/**
	 * @expectedException Nette\DI\MissingServiceException
	 */
	public function testMissingServiceMultipleByType()
	{
		$container = new Container;
		$container->classes[strtolower('Symfony\Component\Console\Application')] = FALSE;
		new LazyRouter($container);
	}

	/**
	 * @expectedException Nette\DI\MissingServiceException
	 */
	public function testMissingServiceNonConsistentContainer()
	{
		$container = new Container;
		$container->classes[strtolower('Symfony\Component\Console\Application')] = 'fake';
		new LazyRouter($container);
	}
}

