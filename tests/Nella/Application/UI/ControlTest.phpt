<?php
/**
 * Test: Nella\Application\UI\Control
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Application\UI\ControlTest
 */

namespace Nella\Tests\Application\UI;

use Assert,
	Nella\Mocks\Application\UI\Presenter;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Application/UI/Presenter.php';
require_once MOCKS_DIR . '/Application/UI/Control.php';

class ControlTest extends \TestCase
{
	/** @var \Nette\DI\Container */
	private $context;
	/** @var ControlMock */
	private $control;

	public function setUp()
	{
		parent::setUp();

		$this->context = new \Nette\DI\Container;

		$this->context->parameters['appDir'] = TESTS_DIR;
		$this->context->parameters['productionMode'] = FALSE;

		$formatter = new \Nella\Templating\TemplateFilesFormatter;
		$formatter->addDir(__DIR__, 5)
			->addDir($this->context->parameters['appDir'], 999)
			->addDir($this->context->expand('%appDir%/Nella'), 0);

		$this->context->addService('nette', new \Nette\DI\NestedAccessor($this->context, 'nette'));
		$this->context->addService('nette.templateCacheStorage', new \Nette\Caching\Storages\DevNullStorage);
		$this->context->addService('nette.httpRequest', new \Nette\Http\Request(new \Nette\Http\UrlScript));
		$this->context->classes['nette\security\user'] = 'nette';
		$this->context->classes['nette\http\iresponse'] = 'nette';
		$this->context->classes['nette\caching\istorage'] = 'nette.templateCacheStorage';
		$this->context->classes['nette\http\irequest'] = 'nette.httpRequest';

		$this->context->extensionMethod('createNette__Latte', function() { return new \Nette\Latte\Engine; });

		$this->context->addService('nella', new \Nette\DI\NestedAccessor($this->context, 'nella'));
		$this->context->addService('nella.templateFilesFormatter', $formatter);

		$this->control = new ControlMock(new Presenter($this->context), 'test');
	}

	public function dataFormatTemplateFiles()
	{
		$context = new \Nette\DI\Container;
		$context->parameters['appDir'] = TESTS_DIR;

		return array(
			array('render', array(
				$context->parameters['appDir'] . "/Tests/Application/UI/templates/ControlMock.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/templates/ControlMock/@global.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/ControlMock.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/ControlMock/@global.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/@global.latte",
				__DIR__ . "/Tests/Application/UI/templates/ControlMock.latte",
				__DIR__ . "/Tests/Application/UI/templates/ControlMock/@global.latte",
				__DIR__ . "/Tests/Application/UI/ControlMock.latte",
				__DIR__ . "/Tests/Application/UI/ControlMock/@global.latte",
				__DIR__ . "/Tests/Application/UI/@global.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/templates/ControlMock.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/templates/ControlMock/@global.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/ControlMock.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/ControlMock/@global.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/@global.latte",
			)),
			array('renderTest', array(
				$context->parameters['appDir'] . "/Tests/Application/UI/templates/ControlMock/test.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/templates/ControlMock.test.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/templates/ControlMock/@global.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/ControlMock/test.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/ControlMock.test.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/ControlMock/@global.latte",
				$context->parameters['appDir'] . "/Tests/Application/UI/@global.latte",
				__DIR__ . "/Tests/Application/UI/templates/ControlMock/test.latte",
				__DIR__ . "/Tests/Application/UI/templates/ControlMock.test.latte",
				__DIR__ . "/Tests/Application/UI/templates/ControlMock/@global.latte",
				__DIR__ . "/Tests/Application/UI/ControlMock/test.latte",
				__DIR__ . "/Tests/Application/UI/ControlMock.test.latte",
				__DIR__ . "/Tests/Application/UI/ControlMock/@global.latte",
				__DIR__ . "/Tests/Application/UI/@global.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/templates/ControlMock/test.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/templates/ControlMock.test.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/templates/ControlMock/@global.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/ControlMock/test.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/ControlMock.test.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/ControlMock/@global.latte",
				$context->expand('%appDir%/Nella') . "/Tests/Application/UI/@global.latte",
			)),
		);
	}

	/**
	 * @dataProvider dataFormatTemplateFiles
	 */
	public function testFormatTemplateFiles($method, $eq)
	{
		$files = $this->control->formatTemplateFilesMock($method);

		Assert::equal($eq, $files, "->formatTemplateFiles('$method')");
	}

	public function testFormatTemplateFile()
	{
		$this->context->getService('nella.templateFilesFormatter')
			->addDir(FIXTURES_DIR);

		Assert::equal(
			FIXTURES_DIR . "/Tests/Application/UI/ControlMock.latte",
			$this->control->formatTemplateFileMock('render'),
			"->formatTemplateFile for default view"
		);
	}

	public function testFormatTemplateFileException()
	{
		$control = $this->control;
		Assert::throws(function() use($control) {
			$control->formatTemplateFileMock('renderFoo');
		}, 'Nette\InvalidStateException');
	}

	public function testRender()
	{
		$this->context->getService('nella.templateFilesFormatter')
			->addDir(FIXTURES_DIR);

		ob_start();
		$this->control->render();
		$data = ob_get_clean();

		Assert::equal('TEST', $data, '->render()');
	}
}

class ControlMock extends \Nella\Mocks\Application\UI\Control {}