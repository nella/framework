<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Event\Args;

class CompilerBeforeTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Event\Args\CompilerBefore */
	private $args;

	public function setup()
	{
		parent::setup();
		$compiler = $this->getMockBuilder('Nette\Config\Compiler')->disableOriginalConstructor()->getMock();
		$builder = $this->getMockBuilder('Nette\DI\ContainerBuilder')->disableOriginalConstructor()->getMock();

		$this->args = new \Nella\Event\Args\CompilerBefore($compiler, $builder);
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Event\EventArgs', $this->args);
	}

	public function testGetCompiler()
	{
		$this->assertInstanceOf('Nette\Config\Compiler', $this->args->getCompiler(), "->getCompiler()");
		$this->assertInstanceOf('Nette\Config\Compiler', $this->args->compiler, "->compiler");
	}

	public function testGetContainerBuilder()
	{
		$this->assertInstanceOf('Nette\DI\ContainerBuilder', $this->args->getContainerBuilder(), "->getContainerBuilder()");
		$this->assertInstanceOf('Nette\DI\ContainerBuilder', $this->args->containerBuilder, "->containerBuilder");
	}
}