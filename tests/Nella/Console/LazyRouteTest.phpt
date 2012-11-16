<?php
/**
 * Test: Nella\Console\LazyRouter
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Console;

use Tester\Assert,
	Nette\DI\Container,
	Nella\Console\LazyRouter;

require_once __DIR__ . '/../../bootstrap.php';

class LazyRouterTest extends \Tester\TestCase
{
	public function testMissingServiceByName()
	{
		Assert::throws(function() {
			$container = new Container;
			new LazyRouter($container, 'console.application');
		}, 'Nette\DI\MissingServiceException');
	}

	public function testMissingServiceByType()
	{
		Assert::throws(function() {
			$container = new Container;
			new LazyRouter($container);
		}, 'Nette\DI\MissingServiceException');
	}

	public function testMissingServiceMultipleByType()
	{
		Assert::throws(function() {
			$container = new Container;
			$container->classes[strtolower('Symfony\Component\Console\Application')] = FALSE;
			new LazyRouter($container);
		}, 'Nette\DI\MissingServiceException');
	}

	public function testMissingServiceNonConsistentContainer()
	{
		Assert::throws(function() {
			$container = new Container;
			$container->classes[strtolower('Symfony\Component\Console\Application')] = 'fake';
			new LazyRouter($container);
		}, 'Nette\DI\MissingServiceException');
	}
}

id(new LazyRouterTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
