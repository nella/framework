<?php
/**
 * Test: Nella\Config\Configurator
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Config\ConfiguratorTest
 */

namespace Nella\Tests\Config;

use Assert;

require_once __DIR__ . '/../../bootstrap.php';

class ConfiguratorTest extends \TestCase
{
	/** @var \Nella\Config\Configurator */
	private $configurator;

	public function setUp()
	{
		parent::setUp();
		$this->configurator = new ConfiguratorMock;
	}

	public function testInstanceOf()
	{
		Assert::true($this->configurator instanceof \Nette\Config\Configurator);
	}

	public function testGetSplClassLoader()
	{
		Assert::true($this->configurator->getSplClassLoader() instanceof \Nella\SplClassLoader, '->getSplClassLoader()');
		Assert::true($this->configurator->splClassLoader instanceof \Nella\SplClassLoader, '->splClassLoader');
	}

	public function testAddConfig()
	{
		$this->configurator->addConfig('foo');
		Assert::equal(array(array('foo', ConfiguratorMock::NONE)), $this->configurator->getFiles());
	}

	public function testAddConfigIfExist()
	{
		$this->configurator->addConfigIfExist(__FILE__);
		Assert::equal(array(array(__FILE__, ConfiguratorMock::NONE)), $this->configurator->getFiles());
	}
	public function testAddConfigIfExistFail()
	{
		$this->configurator->addConfigIfExist('foo');
		Assert::equal(array(), $this->configurator->getFiles());
	}

	public function testGetCompiler()
	{
		$extensions = $this->configurator->createCompilerMock()->getExtensions();

		Assert::true($extensions['constants'] instanceof \Nette\Config\Extensions\ConstantsExtension);
		Assert::true($extensions['php'] instanceof \Nette\Config\Extensions\PhpExtension);
		Assert::true($extensions['nette'] instanceof \Nette\Config\Extensions\NetteExtension);
		Assert::true($extensions['nella'] instanceof \Nella\Config\Extensions\NellaExtension);
	}
}

class ConfiguratorMock extends \Nella\Config\Configurator
{
	public function getFiles()
	{
		return $this->files;
	}

	public function createCompilerMock()
	{
		return $this->createCompiler();
	}
}