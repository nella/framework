<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application\UI;

use Nella\DI\ContextBuilder;

require_once __DIR__ . "/../../bootstrap.php";

class PresenterTest extends \PHPUnit_Framework_TestCase
{
	/** @var PresenterMock */
	private $presenter;

	public function setUp()
	{
		$container = \Nette\Environment::getContext();
		$this->presenter = new PresenterMock;
		$this->presenter->setContext($container);
	}

	public function testFormmatLayoutTemplatesFiles()
	{
		$this->presenter->name = "Foo";
		$this->presenter->layout = "bar";
		$this->assertEquals(array(
			APP_DIR . "/templates/Foo/@bar.latte",
			APP_DIR . "/templates/Foo.@bar.latte",
			APP_DIR . "/templates/@bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/@bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo.@bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/@bar.latte",
		), $this->presenter->formatLayoutTemplateFiles(),
		"->formatLayoutTemplateFiles() Foo:@bar");

		$this->presenter->name = "Foo:Bar";
		$this->presenter->layout = "layout";
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
		), $this->presenter->formatLayoutTemplateFiles(),
		"->formatLayoutTemplateFiles() Foo:Bar:@layout");
	}

	public function testFormatTemplatesFiles()
	{
		$mapper = function ($path) {
			return @realpath($path);
		};

		$this->presenter->name = "Foo";
		$this->presenter->view = "bar";
		$this->assertEquals(array(
			APP_DIR . "/templates/Foo/bar.latte",
			APP_DIR . "/templates/Foo.bar.latte",
			APP_DIR . "/templates/Foo/@global.latte",
			APP_DIR . "/templates/@global.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo.bar.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo/@global.latte",
			NELLA_FRAMEWORK_DIR . "/templates/@global.latte",
		), $this->presenter->formatTemplateFiles(),
		"->formatTemplateFiles() Foo:bar");

		$this->presenter->name = "Foo:Bar";
		$this->presenter->view = "baz";
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
		"->formatTemplateFiles() Foo:Bar:baz");
	}

	public function testGlobalComponent()
	{
		$this->presenter->getContext()->getService('components')
			->addComponent('foo', function($parent, $name) { return "bar"; });

		$this->assertEquals("bar", $this->presenter->createComponentMock('foo'));
		$this->assertNull($this->presenter->createComponentMock('bar'));
	}
}
