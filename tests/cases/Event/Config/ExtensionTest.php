<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Event\Config;

use Nella\Event\Config\Extension;

class ExtensionTest extends \Nella\Testing\TestCase
{
	public function testRegister()
	{
		$configurator = new ConfiguratorMock;
		Extension::register($configurator);
		$compiler = $configurator->createCompilerMock();
		$configurator->onCompile($configurator, $compiler);

		$this->assertInstanceOf('Nella\Event\Config\Extension', $compiler->extensions[Extension::DEFAULT_EXTENSION_NAME]);
	}
}

class ConfiguratorMock extends \Nette\Config\Configurator
{
	public function createCompilerMock()
	{
		return $this->createCompiler();
	}
}

