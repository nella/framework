<?php
/**
 * This file is part of the Nella Framework.
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
		$this->configurator = new \Nella\Config\Configurator;
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
}