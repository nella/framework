<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests;

class SplClassLoaderTest extends \Nella\Testing\TestCase
{
	public function setup()
	{
		parent::setup();
		\Nella\SplClassLoader::getInstance()
			->addNamespaceAlias('NellaTests\SplClassLoader', __DIR__ . "/../fixtures")
			->register();
	}

	public function testLoadClass()
	{
		$this->assertTrue(class_exists('NellaTests\SplClassLoader\Foo'));
	}

	public function testLoadNonExistsClass()
	{
		$this->assertFalse(class_exists('NellaTests\SplClassLoader\Bar'));
	}
}
