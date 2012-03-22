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
			array('render', array(
				$context->parameters['appDir'] . "/Application/UI/Control/ControlMock.latte",
				$context->parameters['appDir'] . "/Application/UI/Control/ControlMock/@global.latte",
				$context->parameters['appDir'] . "/Application/UI/Control/@global.latte",
				__DIR__ . "/Application/UI/Control/ControlMock.latte",
				__DIR__ . "/Application/UI/Control/ControlMock/@global.latte",
				__DIR__ . "/Application/UI/Control/@global.latte",
				$context->expand('%appDir%/Nella') . "/Application/UI/Control/ControlMock.latte",
				$context->expand('%appDir%/Nella') . "/Application/UI/Control/ControlMock/@global.latte",
				$context->expand('%appDir%/Nella') . "/Application/UI/Control/@global.latte",
			)),
			array('renderTest', array(
				$context->parameters['appDir'] . "/Application/UI/Control/ControlMock/test.latte",
				$context->parameters['appDir'] . "/Application/UI/Control/ControlMock.test.latte",
				$context->parameters['appDir'] . "/Application/UI/Control/ControlMock/@global.latte",
				$context->parameters['appDir'] . "/Application/UI/Control/@global.latte",
				__DIR__ . "/Application/UI/Control/ControlMock/test.latte",
				__DIR__ . "/Application/UI/Control/ControlMock.test.latte",
				__DIR__ . "/Application/UI/Control/ControlMock/@global.latte",
				__DIR__ . "/Application/UI/Control/@global.latte",
				$context->expand('%appDir%/Nella') . "/Application/UI/Control/ControlMock/test.latte",
				$context->expand('%appDir%/Nella') . "/Application/UI/Control/ControlMock.test.latte",
				$context->expand('%appDir%/Nella') . "/Application/UI/Control/ControlMock/@global.latte",
				$context->expand('%appDir%/Nella') . "/Application/UI/Control/@global.latte",
			)),
		);
	}

	/**
	 * @dataProvider dataFormatTemplateFiles
	 */
	public function testFormatTemplateFiles($method, $eq)
	{
		$files = $this->control->formatTemplateFilesMock($method);

		$this->assertEquals($eq, $files, "->formatTemplateFiles('$method')");
	}

	public function testFormatTemplateFile()
	{
		$fixturesDir = $this->getContext()->parameters['fixturesDir'];
		$this->getContext()->getService('nella.templateFilesFormatter')
				->addDir($fixturesDir);

		$this->assertEquals(
			$fixturesDir . "/Application/UI/Control/ControlMock.latte",
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