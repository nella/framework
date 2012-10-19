<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Event\Args;

class ContainerTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Event\Args\Container */
	private $args;

	public function setup()
	{
		parent::setup();
		$this->args = new \Nella\Event\Args\Container(new \Nette\DI\Container);
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Event\EventArgs', $this->args);
	}
	
	public function testGetContainer()
	{
		$this->assertInstanceOf('Nette\DI\IContainer', $this->args->getContainer(), "->getContainer()");
		$this->assertInstanceOf('Nette\DI\IContainer', $this->args->container, "->container");
	}
}