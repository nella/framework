<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Application;

require_once __DIR__ . "/../bootstrap.php";

class ControlTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Application\Control */
	private $control;
	
	public function setUp()
	{
		\NellaTests\Application\ControlMock::$namespacePrefixes[] = 'NellaTests\\Application\\';
		$this->control = new ControlMock(new PresenterMock, 'test');
	}
	
	public function testFormatTemplateFiles()
	{
		$this->assertEquals(array(
			APP_DIR . "/Foo/Bar.latte", 
			APP_DIR . "/Templates/Foo/Bar.latte", 
			NELLA_FRAMEWORK_DIR . "/Foo/Bar.latte", 
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/Bar.latte", 
		), 
		$this->control->formatTemplateFilesMock('Nella\Foo\Bar::render'), "->formatTemplateFiles() for Foo\\Bar::render");
		
		$this->assertEquals(array(
			APP_DIR . "/Foo/Bar.baz.latte", 
			APP_DIR . "/Templates/Foo/Bar.baz.latte", 
			NELLA_FRAMEWORK_DIR . "/Foo/Bar.baz.latte", 
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/Bar.baz.latte", 
		), 
		$this->control->formatTemplateFilesMock('Nella\Foo\Bar::renderBaz'), "->formatTemplateFiles() for Foo\\Bar::renderBaz");
		
		$this->assertEquals(array(
			APP_DIR . "/Foo.barBaz.latte", 
			APP_DIR . "/Templates/Foo.barBaz.latte", 
			NELLA_FRAMEWORK_DIR . "/Foo.barBaz.latte", 
			NELLA_FRAMEWORK_DIR . "/Templates/Foo.barBaz.latte", 
		), 
		$this->control->formatTemplateFilesMock('Nella\Foo::renderBarBaz'), "->formatTemplateFiles() for Foo::renderBarBaz");
	}
	
	public function testFormatTemplateFile()
	{
		$this->assertEquals(APP_DIR . "/ControlMock.latte", $this->control->formatTemplateFileMock('render'), "->formatTemplateFile for defautl view");
	}
	
	/**
  	 * @expectedException InvalidStateException
	 */
	public function testFormatTemplateFileException()
  	{
		$this->control->formatTemplateFileMock('renderFoo');
	}
	
	public function testRender()
	{
		$this->markTestSkipped("because not set tempDir");
		ob_start();
		$this->control->render();
		$data = ob_get_clean();
		
		$this->assertEquals("TEST", $data, "->render()");
	}
}
