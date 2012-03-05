<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests;

class SplClassLoaderTest extends \Nella\Testing\TestCase
{
	public function setup()
	{
		parent::setup();
		\Nella\SplClassLoader::getInstance()->addNamespaceAlias('NellaTests\SplClassLoader', __DIR__ . "/../fixtures");
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
