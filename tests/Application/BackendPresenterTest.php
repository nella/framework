<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application;

require_once __DIR__ . "/../bootstrap.php";

class BackendPresenterTest extends \PHPUnit_Framework_TestCase
{
	/** @var BackendPresenterMock */
	private $presenter;
	
	public function setUp()
	{
		$this->presenter = new BackendPresenterMock;
	}
	
	/**
	 * @expectedException Nette\Application\BadRequestException
	 */
	public function testActionDeny()
	{
		$this->presenter->changeAction("test1");
		$this->presenter->startupMock();
	}
	
	public function testActionAllow()
	{
		$this->presenter->changeAction("test2");
		$this->presenter->startupMock();
		
		$this->assertFalse($this->presenter->isAllowedMock('actionTest1'));
		$this->assertTrue($this->presenter->isAllowedMock('actionTest2'));
	}
	
	/**
	 * @expectedException Nette\Application\BadRequestException
	 */
	public function testViewDeny()
	{
		$this->presenter->setView("test1");
		$this->presenter->startupMock();
	}
	
	public function testViewAllow()
	{
		$this->presenter->setView("test2");
		$this->presenter->startupMock();
		
		$this->assertFalse($this->presenter->isAllowedMock('renderTest1'));
		$this->assertTrue($this->presenter->isAllowedMock('renderTest2'));
	}
	
	/**
	 * @expectedException Nette\Application\BadRequestException
	 */
	public function testSignalDeny()
	{
		$this->presenter->setSignal("test1");
		$this->presenter->startupMock();
	}
	
	public function testSignalAllow()
	{
		$this->presenter->setSignal("test2");
		$this->presenter->startupMock();
		
		$this->assertFalse($this->presenter->isAllowedMock('handleTest1'));
		$this->assertTrue($this->presenter->isAllowedMock('handleTest2'));
	}
	
	/**
	 * @expectedException Nette\Application\BadRequestException
	 */
	public function testComponentDeny()
	{
		$this->presenter->createComponentMock('test1');
	}
	
	public function testComponentAllow()
	{
		$this->assertNull($this->presenter->createComponentMock('test2'));
		
		$this->assertFalse($this->presenter->isAllowedMock('createComponentTest1'));
		$this->assertTrue($this->presenter->isAllowedMock('createComponentTest2'));
	}
	
	public function testLayout()
	{
		$this->presenter->startupMock();
		$this->assertEquals("backend", $this->presenter->layout);
	}
	
	public function testLang()
	{
		$this->presenter->startupMock();
		$this->assertEquals("es", $this->presenter->lang);
	}
}