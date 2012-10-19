<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Event\Args;

class ApplicationTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Event\Args\Application */
	private $args;

	public function setup()
	{
		parent::setup();
		$application = $this->getMockBuilder('Nette\Application\Application')->disableOriginalConstructor()->getMock();

		$this->args = new \Nella\Event\Args\Application($application);
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Event\EventArgs', $this->args);
	}
	
	public function testGetApplication()
	{
		$this->assertInstanceOf('Nette\Application\Application', $this->args->getApplication(), "->getApplication()");
		$this->assertInstanceOf('Nette\Application\Application', $this->args->application, "->application");
	}
}