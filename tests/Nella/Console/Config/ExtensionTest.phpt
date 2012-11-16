<?php
/**
 * Test: Nella\Console\Config\Extension
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Console\Config;

use Tester\Assert,
	Nella\Mocks\Config\Configurator,
	Nella\Mocks\Config\Compiler,
	Nella\Console\Config\Extension,
	Nette\DI\Container,
	Symfony\Component\Console\Helper\HelperSet,
	Symfony\Component\Console\Helper\DialogHelper;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Config/Configurator.php';
require_once MOCKS_DIR . '/Config/Compiler.php';

class ExtensionTest extends \Tester\TestCase
{
	public function testRegister()
	{
		$configurator = new Configurator;
		Extension::register($configurator);
		$compiler = $configurator->createCompilerMock();
		$configurator->onCompile($configurator, $compiler);

		$extension = $compiler->extensions[Extension::DEFAULT_EXTENSION_NAME];
		Assert::true($extension instanceof Extension);
	}

	public function testLoadConfiguration()
	{
		$compiler = new Compiler;
		$builder = $compiler->getContainerBuilder();
		$builder->parameters['productionMode'] = TRUE;
		$extension = new Extension;
		$extension->setCompiler($compiler, 'console');

		$extension->loadConfiguration();

		Assert::true($builder->hasDefinition($extension->prefix('helperset')), 'has helperset service');
		Assert::true($builder->hasDefinition($extension->prefix('application')), 'has application service');
	}

	public function testBasicHelperSet()
	{
		$container = new Container;
		$helperSet = Extension::createHelperSet($container);

		Assert::true($helperSet instanceof HelperSet, 'instance');

		Assert::true($helperSet->has('dialog'), 'has dialog helper');
		Assert::false($helperSet->has('foo'), 'has not foo helper');
		Assert::true($helperSet->get('dialog') instanceof DialogHelper, 'dialog instance');
	}

	public function testHelperSet()
	{
		$container = new Container;

		$container->addService('test', new \Symfony\Component\Console\Helper\DialogHelper, array(
			Container::TAGS => array(Extension::HELPER_TAG_NAME => 'foo'),
		));

		$helperSet = Extension::createHelperSet($container);

		Assert::true($helperSet->has('foo'), 'has foo helper');
		Assert::true($helperSet->get('foo') instanceof DialogHelper, 'foo instance');
		Assert::same($container->test, $helperSet->get('foo'), 'foo helper same as test service');
	}

	public function testBasicApplication()
	{
		$container = new Container;
		$helperSet = new HelperSet;
		$application = Extension::createApplication('foo', '1.0', $helperSet, TRUE, $container);

		Assert::true($application instanceof \Symfony\Component\Console\Application, 'instance');

		Assert::same($application->getHelperSet(), $helperSet, 'same helperset');

		Assert::equal('foo', $application->getName(), 'name');
		Assert::equal('1.0', $application->getVersion(), 'version');
		Assert::false($application->has('foo'), 'no foo command');
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

		Assert::true($application->has('foo'), 'yes foo command');
		Assert::true($application->get('foo') instanceof \Symfony\Component\Console\Command\Command, 'instance');
		Assert::same($cmd, $application->get('foo'), 'foo command same as test service');
	}
}

id(new ExtensionTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
