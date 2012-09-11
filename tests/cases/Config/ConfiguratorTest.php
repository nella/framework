<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Config;

class ConfiguratorTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Config\Configurator */
	private $configurator;

	public function setup()
	{
		parent::setup();
		$this->configurator = new ConfiguratorMock;
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nette\Config\Configurator', $this->configurator);
	}

	public function testGetSplClassLoader()
	{
		$this->assertInstanceOf('Nella\SplClassLoader', $this->configurator->getSplClassLoader(), '->getSplClassLoader()');
		$this->assertInstanceOf('Nella\SplClassLoader', $this->configurator->splClassLoader, '->splClassLoader');
	}

	public function testAddConfig()
	{
		$this->configurator->addConfig('foo');
		$this->assertEquals(array(array('foo', ConfiguratorMock::NONE)), $this->configurator->getFiles());
	}

	public function testAddConfigIfExist()
	{
		$this->configurator->addConfigIfExist(__FILE__);
		$this->assertEquals(array(array(__FILE__, ConfiguratorMock::NONE)), $this->configurator->getFiles());
	}
	public function testAddConfigIfExistFail()
	{
		$this->configurator->addConfigIfExist('foo');
		$this->assertEquals(array(), $this->configurator->getFiles());
	}

	public function testGetCompiler()
	{
		$extensions = $this->configurator->createCompilerMock()->getExtensions();

		$this->assertInstanceOf('Nette\Config\Extensions\ConstantsExtension', $extensions['constants']);
		$this->assertInstanceOf('Nette\Config\Extensions\PhpExtension', $extensions['php']);
		$this->assertInstanceOf('Nette\Config\Extensions\NetteExtension', $extensions['nette']);
		$this->assertInstanceOf('Nella\Config\Extensions\NellaExtension', $extensions['nella']);
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