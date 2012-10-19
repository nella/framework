<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Event\Args;

class ApplicationResponseTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Event\Args\ApplicationResponse */
	private $args;

	public function setup()
	{
		parent::setup();
		$application = $this->getMockBuilder('Nette\Application\Application')->disableOriginalConstructor()->getMock();
		$response = $this->getMock('Nette\Application\IResponse');

		$this->args = new \Nella\Event\Args\ApplicationResponse($application, $response);
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
	
	public function testGetResponse()
	{
		$this->assertInstanceOf('Nette\Application\IResponse', $this->args->getResponse(), "->getResponse()");
		$this->assertInstanceOf('Nette\Application\IResponse', $this->args->response, "->response");
	}
}