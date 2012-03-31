<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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