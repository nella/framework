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

class ControlTest extends \PHPUnit_Framework_TestCase
{
	/** @var ControlMock */
	private $control;

	public function setUp()
	{
		$context = \Nette\Environment::getContext();
		$prefixies = $context->getParam('prefixies');
		$prefixies[] = 'NellaTests\\Application\\UI\\';
		$context->setParam('prefixies', $prefixies);
		$this->control = new ControlMock(new PresenterMock, 'test');
		$this->control->presenter->setContext($context);
	}

	public function testFormatTemplateFiles()
	{
		$this->assertEquals(array(
				APP_DIR . "/Foo/Bar.latte",
				APP_DIR . "/templates/Foo/Bar.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/Bar.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar.latte",
			),
			$this->control->formatTemplateFilesMock('Nella\Foo\Bar::render'), 
			"->formatTemplateFiles() for Foo\\Bar::render"
		);

		$this->assertEquals(array(
				APP_DIR . "/Foo/Bar.baz.latte",
				APP_DIR . "/templates/Foo/Bar.baz.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/Bar.baz.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar.baz.latte",
			),
			$this->control->formatTemplateFilesMock('Nella\Foo\Bar::renderBaz'), 
			"->formatTemplateFiles() for Foo\\Bar::renderBaz"
		);

		$this->assertEquals(array(
			APP_DIR . "/Foo.barBaz.latte",
			APP_DIR . "/templates/Foo.barBaz.latte",
			NELLA_FRAMEWORK_DIR . "/Foo.barBaz.latte",
			NELLA_FRAMEWORK_DIR . "/templates/Foo.barBaz.latte",
		),
		$this->control->formatTemplateFilesMock('Nella\Foo::renderBarBaz'), "->formatTemplateFiles() for Foo::renderBarBaz");
	}

	public function testFormatTemplateFile()
	{
		$this->assertEquals(APP_DIR . "/ControlMock.latte", $this->control->formatTemplateFileMock('render'), "->formatTemplateFile for defautl view");
	}

	/**
  	 * @expectedException Nette\InvalidStateException
	 */
	public function testFormatTemplateFileException()
  	{
		$this->control->formatTemplateFileMock('renderFoo');
	}

	public function testRender()
	{
		ob_start();
		$this->control->render();
		$data = ob_get_clean();

		$this->assertEquals("TEST", $data, "->render()");
	}

	public function testGlobalComponent()
	{
		$this->control->presenter->context->getService('components')
			->addComponent('foo', function($parent, $name) { return "bar"; });

		$this->assertEquals("bar", $this->control->createComponentMock('foo'));
		$this->assertNull($this->control->createComponentMock('bar'));
	}
}
