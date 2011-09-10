<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application\UI;

class ControlTest extends \Nella\Testing\TestCase
{
	/** @var Control\ControlMock */
	private $control;

	public function setup()
	{
		parent::setup();
		$this->context->params['namespaces'] = array('App', 'NellaTests\Application\UI\Control', 'Nella');
		$this->context->params['templates'] = array(
			$this->context->params['appDir'],
			__DIR__,
			NELLA_FRAMEWORK_DIR,
		);
		$this->control = new Control\ControlMock(new Control\PresenterMock, 'test');
		$this->control->presenter->setContext($this->context);
	}

	public function dataFormatTemplateFiles()
	{
		$context = \Nette\Environment::getContext();
		return array(
			array('Nella\Foo\Bar::render', array(
				$context->params['appDir'] . "/Foo/Bar.latte",
				$context->params['appDir'] . "/templates/Foo/Bar.latte",
				__DIR__ . "/Foo/Bar.latte",
				__DIR__ . "/templates/Foo/Bar.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/Bar.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar.latte",
			)),
			array('Nella\Foo\Bar::renderBaz', array(
				$context->params['appDir'] . "/Foo/Bar.baz.latte",
				$context->params['appDir'] . "/templates/Foo/Bar.baz.latte",
				__DIR__ . "/Foo/Bar.baz.latte",
				__DIR__ . "/templates/Foo/Bar.baz.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/Bar.baz.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar.baz.latte",
			)),
			array('Nella\Foo::renderBarBaz', array(
				$context->params['appDir'] . "/Foo.barBaz.latte",
				$context->params['appDir'] . "/templates/Foo.barBaz.latte",
				__DIR__ . "/Foo.barBaz.latte",
				__DIR__ . "/templates/Foo.barBaz.latte",
				NELLA_FRAMEWORK_DIR . "/Foo.barBaz.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo.barBaz.latte",
			)),
		);
	}

	/**
	 * @dataProvider dataFormatTemplateFiles
	 */
	public function testFormatTemplateFiles($method, $eq)
	{
		$this->assertEquals($eq, $this->control->formatTemplateFilesMock($method), "->formatTemplateFiles('$method')");
	}

	public function testFormatTemplateFile()
	{
		$this->assertEquals(
			__DIR__ . "/ControlMock.latte",
			$this->control->formatTemplateFileMock('render'),
			"->formatTemplateFile for defautl view"
		);
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

namespace NellaTests\Application\UI\Control;

class PresenterMock extends \Nella\Application\UI\Presenter { }

class ControlMock extends \Nella\Application\UI\Control
{
	public function formatTemplateFilesMock($method)
	{
		return $this->formatTemplateFiles($method);
	}

	public function formatTemplateFileMock($method)
	{
		return $this->formatTemplateFile($method);
	}

	public function render()
	{
		$this->_render(__METHOD__);
	}

	public function createComponentMock($name)
	{
		return $this->createComponent($name);
	}
}