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

		$context = $this->getContext();

		$formatter = new \Nella\Templating\TemplateFilesFormatter;
		$formatter->useModuleSuffix = FALSE;
		$formatter->addDir(__DIR__, 5)
				->addDir($context->parameters['appDir'], 999)
				->addDir($context->expand('%appDir%/Nella'), 0);

		if (!$context->hasService('nella')) {
			$context->addService('nella', new \Nette\DI\NestedAccessor($context, 'nella'));
		}

		$context->removeService('nella.templateFilesFormatter');
		$context->addService('nella.templateFilesFormatter', $formatter);

		$this->control = new Control\ControlMock(new Control\PresenterMock($context), 'test');
	}

	public function dataFormatTemplateFiles()
	{
		$context = $this->getContext();
		return array(
			array('Nella\Foo\Bar::render', array(
				$context->parameters['appDir'] . "/templates/Foo/Bar.latte",
				$context->parameters['appDir'] . "/templates/Foo.Bar.latte",
				$context->parameters['appDir'] . "/templates/Foo/@global.latte",
				$context->parameters['appDir'] . "/templates/@global.latte",
				__DIR__ . "/templates/Foo/Bar.latte",
				__DIR__ . "/templates/Foo.Bar.latte",
				__DIR__ . "/templates/Foo/@global.latte",
				__DIR__ . "/templates/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo.Bar.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/@global.latte",
			)),
			array('Nella\Foo\Bar\Baz::renderTest', array(
				$context->parameters['appDir'] . "/Foo/templates/Bar/Baz.test.latte",
				$context->parameters['appDir'] . "/Foo/templates/Bar.Baz.test.latte",
				$context->parameters['appDir'] . "/Foo/templates/Bar/@global.latte",
				$context->parameters['appDir'] . "/Foo/templates/@global.latte",
				$context->parameters['appDir'] . "/templates/Foo/Bar/Baz.test.latte",
				$context->parameters['appDir'] . "/templates/Foo/Bar.Baz.test.latte",
				$context->parameters['appDir'] . "/templates/Foo/Bar/@global.latte",
				$context->parameters['appDir'] . "/templates/Foo/@global.latte",
				$context->parameters['appDir'] . "/templates/@global.latte",
				__DIR__ . "/Foo/templates/Bar/Baz.test.latte",
				__DIR__ . "/Foo/templates/Bar.Baz.test.latte",
				__DIR__ . "/Foo/templates/Bar/@global.latte",
				__DIR__ . "/Foo/templates/@global.latte",
				__DIR__ . "/templates/Foo/Bar/Baz.test.latte",
				__DIR__ . "/templates/Foo/Bar.Baz.test.latte",
				__DIR__ . "/templates/Foo/Bar/@global.latte",
				__DIR__ . "/templates/Foo/@global.latte",
				__DIR__ . "/templates/@global.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/Bar/Baz.test.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/Bar.Baz.test.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/Bar/@global.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar/Baz.test.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar.Baz.test.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/@global.latte",
			)),/*
			array('Nella\Foo::renderBarBaz', array(
				$context->parameters['appDir'] . "/templates/Foo.barBaz.latte",
				__DIR__ . "/templates/Foo.barBaz.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo.barBaz.latte",
			)),*/
		);
	}

	/**
	 * @dataProvider dataFormatTemplateFiles
	 */
	public function testFormatTemplateFiles($method, $eq)
	{
		$files = $this->control->formatTemplateFilesMock($method);

		//var_dump($files);
		//var_dump($eq);

		//exit(255);

		$this->assertEquals($eq, $files, "->formatTemplateFiles('$method')");
	}

	public function testFormatTemplateFile()
	{
		$fixturesDir = $this->getContext()->parameters['fixturesDir'];
		$this->getContext()->getService('nella.templateFilesFormatter')
				->addDir($fixturesDir);

		$this->assertEquals(
			$fixturesDir . "/templates/Application/UI/Control/ControlMock.latte",
			$this->control->formatTemplateFileMock('render'),
			"->formatTemplateFile for default view"
		);
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testFormatTemplateFileException()
	{
		$this->control->formatTemplateFileMock('renderFoo');
	}

	/**
	 * @depends testFormatTemplateFile
	 */
	public function testRender()
	{
		$this->getContext()->getService('nella.templateFilesFormatter')
				->addDir($this->getContext()->parameters['fixturesDir']);

		ob_start();
		$this->control->render();
		$data = ob_get_clean();

		$this->assertEquals("TEST", $data, "->render()");
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
}