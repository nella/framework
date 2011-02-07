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

class PresenterTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Application\Presenter */
	private $presenter;
	
	public function setUp()
	{
		$this->presenter = new PresenterMock;
	}

	/**
	 * @covers Nella\Application\Presenter::formatLayoutTemplateFiles
	 */
	public function testFormmatLayoutTemplatesFiles()
	{
		$this->assertEquals(array(
			APP_DIR . "/Templates/Foo/@bar.latte",
			APP_DIR . "/Templates/Foo.@bar.latte",
			APP_DIR . "/Templates/@bar.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/@bar.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo.@bar.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/@bar.latte",
		), $this->presenter->formatLayoutTemplateFiles('Foo', 'bar'), 
		"->formatLayoutTemplateFiles('Foo', 'bar')");
		
		$this->assertEquals(array(
			APP_DIR . "/Foo/Templates/Bar/@layout.latte",
			APP_DIR . "/Foo/Templates/Bar.@layout.latte",
			APP_DIR . "/Foo/Templates/@layout.latte",
			APP_DIR . "/Templates/Foo/Bar/@layout.latte",
			APP_DIR . "/Templates/Foo/Bar.@layout.latte",
			APP_DIR . "/Templates/Foo/@layout.latte",
			APP_DIR . "/Templates/@layout.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/Templates/Bar/@layout.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/Templates/Bar.@layout.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/Templates/@layout.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/Bar/@layout.latte", 
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/Bar.@layout.latte", 
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/@layout.latte", 
			NELLA_FRAMEWORK_DIR . "/Templates/@layout.latte",
		), $this->presenter->formatLayoutTemplateFiles('Foo:Bar', 'layout'), 
		"->formatLayoutTemplateFiles('Foo:Bar', 'layout')");
	}

	/**
	 * @covers Nella\Application\Presenter::formatTemplateFiles
	 */
	public function testFormatTemplatesFiles()
	{
		$mapper = function ($path) {
			return @realpath($path);
		};
		
		$this->assertEquals(array(
			APP_DIR . "/Templates/Foo/bar.latte",
			APP_DIR . "/Templates/Foo.bar.latte",
			APP_DIR . "/Templates/Foo/@global.latte",
			APP_DIR . "/Templates/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/bar.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo.bar.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/@global.latte",
		), $this->presenter->formatTemplateFiles('Foo', 'bar'), 
		"->formatTemplateFiles('Foo', 'bar')");
		
		$this->assertEquals(array(
			APP_DIR . "/Foo/Templates/Bar/baz.latte",
			APP_DIR . "/Foo/Templates/Bar.baz.latte",
			APP_DIR . "/Foo/Templates/Bar/@global.latte",
			APP_DIR . "/Foo/Templates/@global.latte",
			APP_DIR . "/Templates/Foo/Bar/baz.latte",
			APP_DIR . "/Templates/Foo/Bar.baz.latte",
			APP_DIR . "/Templates/Foo/Bar/@global.latte",
			APP_DIR . "/Templates/Foo/@global.latte",
			APP_DIR . "/Templates/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/Templates/Bar/baz.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/Templates/Bar.baz.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/Templates/Bar/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/Templates/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/Bar/baz.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/Bar.baz.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/Bar/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/Foo/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Templates/@global.latte",
		), $this->presenter->formatTemplateFiles('Foo:Bar', 'baz'), 
		"->formatTemplateFiles('Foo:Bar', 'baz')");
	}

	/**
	 * @covers Nella\Application\Presenter::getContext
	 */
	public function testGetContext()
	{
		$this->assertInstanceOf('Nette\Context', $this->presenter->getContext());
		$this->assertInstanceOf('Nette\Context', $this->presenter->context);
	}
}