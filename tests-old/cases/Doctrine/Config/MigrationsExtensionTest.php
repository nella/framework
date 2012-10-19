<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Doctrine\Config;

use Nella\Doctrine\Config\MigrationsExtension;

class MigrationsExtensionTest extends \Nella\Testing\TestCase
{
	public function testRegister()
	{
		$configurator = new MigrationsConfiguratorMock;
		MigrationsExtension::register($configurator);
		$compiler = $configurator->createCompilerMock();
		$configurator->onCompile($configurator, $compiler);

		$this->assertInstanceOf('Nella\Doctrine\Config\MigrationsExtension', $compiler->extensions[MigrationsExtension::DEFAULT_EXTENSION_NAME]);
	}
}

class MigrationsConfiguratorMock extends \Nette\Config\Configurator
{
	public function createCompilerMock()
	{
		return $this->createCompiler();
	}
}

