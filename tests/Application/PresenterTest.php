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

class PresenterTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Application\Presenter */
	private $presenter;
	
	public function setUp()
	{
		$this->presenter = new PresenterMock;
	}

	public function testFormmatLayoutTemplatesFiles()
	{
		$this->assertEquals(array(
			APP_DIR . "/templates/Foo/@bar.latte",
			APP_DIR . "/templates/Foo.@bar.latte",
			APP_DIR . "/templates/@bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/@bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo.@bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/@bar.latte",
		), $this->presenter->formatLayoutTemplateFiles('Foo', 'bar'), 
		"->formatLayoutTemplateFiles('Foo', 'bar')");
		
		$this->assertEquals(array(
			APP_DIR . "/Foo/templates/Bar/@layout.latte",
			APP_DIR . "/Foo/templates/Bar.@layout.latte",
			APP_DIR . "/Foo/templates/@layout.latte",
			APP_DIR . "/templates/Foo/Bar/@layout.latte",
			APP_DIR . "/templates/Foo/Bar.@layout.latte",
			APP_DIR . "/templates/Foo/@layout.latte",
			APP_DIR . "/templates/@layout.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar/@layout.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar.@layout.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/templates/@layout.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar/@layout.latte", 
			NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar.@layout.latte", 
			NELLA_FRAMEWORK_DIR . "/templates/Foo/@layout.latte", 
			NELLA_FRAMEWORK_DIR . "/templates/@layout.latte",
		), $this->presenter->formatLayoutTemplateFiles('Foo:Bar', 'layout'), 
		"->formatLayoutTemplateFiles('Foo:Bar', 'layout')");
	}

	public function testFormatTemplatesFiles()
	{
		$mapper = function ($path) {
			return @realpath($path);
		};
		
		$this->assertEquals(array(
			APP_DIR . "/templates/Foo/bar.latte",
			APP_DIR . "/templates/Foo.bar.latte",
			APP_DIR . "/templates/Foo/@global.latte",
			APP_DIR . "/templates/@global.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo.bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/@global.latte",
			NELLA_FRAMEWORK_DIR . "/templates/@global.latte",
		), $this->presenter->formatTemplateFiles('Foo', 'bar'), 
		"->formatTemplateFiles('Foo', 'bar')");
		
		$this->assertEquals(array(
			APP_DIR . "/Foo/templates/Bar/baz.latte",
			APP_DIR . "/Foo/templates/Bar.baz.latte",
			APP_DIR . "/Foo/templates/Bar/@global.latte",
			APP_DIR . "/Foo/templates/@global.latte",
			APP_DIR . "/templates/Foo/Bar/baz.latte",
			APP_DIR . "/templates/Foo/Bar.baz.latte",
			APP_DIR . "/templates/Foo/Bar/@global.latte",
			APP_DIR . "/templates/Foo/@global.latte",
			APP_DIR . "/templates/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar/baz.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar.baz.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar/@global.latte",
			NELLA_FRAMEWORK_DIR . "/Foo/templates/@global.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar/baz.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar.baz.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar/@global.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/@global.latte",
			NELLA_FRAMEWORK_DIR . "/templates/@global.latte",
		), $this->presenter->formatTemplateFiles('Foo:Bar', 'baz'), 
		"->formatTemplateFiles('Foo:Bar', 'baz')");
	}
}
