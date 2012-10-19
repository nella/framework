<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Console\Config;

use Nella\Console\Config\Extension,
	Nette\DI\Container,
	Symfony\Component\Console\Helper\HelperSet;

class ExtensionTest extends \Nella\Testing\TestCase
{
	public function testLoadConfiguration()
	{
		$builder = new \Nette\DI\ContainerBuilder;
		$builder->parameters['productionMode'] = TRUE;
		$compiler = $this->getMock('Nette\Config\Compiler');
		$compiler->expects($this->any())->method('getContainerBuilder')->will($this->returnValue($builder));
		$extension = new Extension;
		$extension->setCompiler($compiler, 'console');

		$extension->loadConfiguration();

		$this->assertTrue($builder->hasDefinition($extension->prefix('helperset')), 'has helperset service');
		$this->assertTrue($builder->hasDefinition($extension->prefix('application')), 'has application service');
	}

	public function testBasicHelperSet()
	{
		$container = new Container;
		$helperSet = Extension::createHelperSet($container);

		$this->assertInstanceOf('Symfony\Component\Console\Helper\HelperSet', $helperSet, 'instance');

		$this->assertTrue($helperSet->has('dialog'), 'has dialog helper');
		$this->assertFalse($helperSet->has('foo'), 'has not foo helper');
		$this->assertInstanceOf('Symfony\Component\Console\Helper\DialogHelper', $helperSet->get('dialog'), 'dialog instance');
	}

	public function testHelperSet()
	{
		$container = new Container;

		$container->addService('test', new \Symfony\Component\Console\Helper\DialogHelper, array(
			Container::TAGS => array(Extension::HELPER_TAG_NAME => 'foo'),
		));

		$helperSet = Extension::createHelperSet($container);

		$this->assertTrue($helperSet->has('foo'), 'has foo helper');
		$this->assertInstanceOf('Symfony\Component\Console\Helper\DialogHelper', $helperSet->get('foo'), 'foo instance');
		$this->assertSame($container->test, $helperSet->get('foo'), 'foo helper same as test service');
	}

	public function testBasicApplication()
	{
		$container = new Container;
		$helperSet = new HelperSet;
		$application = Extension::createApplication('foo', '1.0', $helperSet, TRUE, $container);

		$this->assertInstanceOf('Symfony\Component\Console\Application', $application, 'instance');

		$this->assertSame($application->getHelperSet(), $helperSet, 'same helperset');

		$this->assertEquals('foo', $application->getName(), 'name');
		$this->assertEquals('1.0', $application->getVersion(), 'version');
		$this->assertFalse($application->has('foo'), 'no foo command');
	}

	public function testApplication()
	{
		$container = new Container;
		$helperSet = new HelperSet;
		$cmd = new \Symfony\Component\Console\Command\Command('foo');

		$container->addService('test', $cmd, array(
			Container::TAGS => array(Extension::COMMAND_TAG_NAME => TRUE),
		));

		$application = Extension::createApplication('bar', '2.0', $helperSet, FALSE, $container);

		$this->assertTrue($application->has('foo'), 'yes foo command');
		$this->assertInstanceOf('Symfony\Component\Console\Command\Command', $application->get('foo'), 'instance');
		$this->assertSame($cmd, $application->get('foo'), 'foo command same as test service');
	}

	public function testRegister()
	{
		$configurator = new ConfiguratorMock;
		Extension::register($configurator);
		$compiler = $configurator->createCompilerMock();
		$configurator->onCompile($configurator, $compiler);

		$this->assertInstanceOf('Nella\Console\Config\Extension', $compiler->extensions[Extension::DEFAULT_EXTENSION_NAME]);
	}
}

class ConfiguratorMock extends \Nette\Config\Configurator
{
	public function createCompilerMock()
	{
		return $this->createCompiler();
	}
}

