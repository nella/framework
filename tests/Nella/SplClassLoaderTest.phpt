<?php
/**
 * Test: Nella\SplClassLoaderTest
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests;

use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class SplClassLoaderTest extends \Tester\TestCase
{
	public function setUp()
	{
		parent::setUp();
		\Nella\SplClassLoader::getInstance()
			->addNamespaceAlias('NellaTests\SplClassLoader', FIXTURES_DIR)
			->register();
	}

	public function testLoadClass()
	{
		Assert::true(class_exists('NellaTests\SplClassLoader\Foo'));
	}

	public function testLoadNonExistsClass()
	{
		Assert::false(class_exists('NellaTests\SplClassLoader\Bar'));
	}
}

id(new SplClassLoaderTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
