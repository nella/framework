<?php
/**
 * Test: Nella\Application\UI\Presenter
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Application\UI;

use Tester\Assert,
	Nella\Mocks\Application\UI\Presenter;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Application/UI/Presenter.php';

class PresenterTest extends \Tester\TestCase
{
	/** @var \Nette\DI\Container */
	private $context;
	/** @var \Nella\Mocks\Application\UI\Presenter */
	private $presenter;

	public function setUp()
	{
		parent::setUp();

		$this->context = new \Nette\DI\Container;

		$this->context->parameters['appDir'] = TESTS_DIR;
		$this->context->parameters['productionMode'] = FALSE;

		$formatter = new \Nella\Templating\TemplateFilesFormatter;
		$formatter->useModuleSuffix = FALSE;
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

		$this->presenter = new Presenter($this->context);
	}

	public function dataFormatLayoutTemplatesFiles()
	{
		$context = new \Nette\DI\Container;
		$context->parameters['appDir'] = TESTS_DIR;

		return array(
			array('Foo', 'bar', array(
				$context->parameters['appDir'] . "/templates/Foo/@bar.latte",
				$context->parameters['appDir'] . "/templates/Foo.@bar.latte",
				$context->parameters['appDir'] . "/templates/@bar.latte",
				__DIR__ . "/templates/Foo/@bar.latte",
				__DIR__ . "/templates/Foo.@bar.latte",
				__DIR__ . "/templates/@bar.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/@bar.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo.@bar.latte",
				$context->expand('%appDir%/Nella') . "/templates/@bar.latte",
			)),
			array('Foo:Bar', 'layout', array(
				$context->parameters['appDir'] . "/Foo/templates/Bar/@layout.latte",
				$context->parameters['appDir'] . "/Foo/templates/Bar.@layout.latte",
				$context->parameters['appDir'] . "/Foo/templates/@layout.latte",
				$context->parameters['appDir'] . "/templates/Foo/Bar/@layout.latte",
				$context->parameters['appDir'] . "/templates/Foo/Bar.@layout.latte",
				$context->parameters['appDir'] . "/templates/Foo/@layout.latte",
				$context->parameters['appDir'] . "/templates/@layout.latte",
				__DIR__ . "/Foo/templates/Bar/@layout.latte",
				__DIR__ . "/Foo/templates/Bar.@layout.latte",
				__DIR__ . "/Foo/templates/@layout.latte",
				__DIR__ . "/templates/Foo/Bar/@layout.latte",
				__DIR__ . "/templates/Foo/Bar.@layout.latte",
				__DIR__ . "/templates/Foo/@layout.latte",
				__DIR__ . "/templates/@layout.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/Bar/@layout.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/Bar.@layout.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/@layout.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar/@layout.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar.@layout.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/@layout.latte",
				$context->expand('%appDir%/Nella') . "/templates/@layout.latte",
			)),
		);
	}

	/**
	 * @dataProvider dataFormatLayoutTemplatesFiles
	 */
	public function testFormatLayoutTemplatesFiles($presenter, $layout, $eq)
	{
		$this->presenter->name = $presenter;
		$this->presenter->layout = $layout;

		Assert::equal($eq, $this->presenter->formatLayoutTemplateFiles(), "->formatLayoutTemplateFiles() $presenter:@$layout");
	}

	public function dataFormatTemplatesFiles()
	{
		$context = new \Nette\DI\Container;
		$context->parameters['appDir'] = TESTS_DIR;

		return array(
			array('Foo', 'bar', array(
				$context->parameters['appDir'] . "/templates/Foo/bar.latte",
				$context->parameters['appDir'] . "/templates/Foo.bar.latte",
				$context->parameters['appDir'] . "/templates/Foo/@global.latte",
				$context->parameters['appDir'] . "/templates/@global.latte",
				__DIR__ . "/templates/Foo/bar.latte",
				__DIR__ . "/templates/Foo.bar.latte",
				__DIR__ . "/templates/Foo/@global.latte",
				__DIR__ . "/templates/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/bar.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo.bar.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/@global.latte",
			)),
			array('Foo:Bar', 'baz', array(
				$context->parameters['appDir'] . "/Foo/templates/Bar/baz.latte",
				$context->parameters['appDir'] . "/Foo/templates/Bar.baz.latte",
				$context->parameters['appDir'] . "/Foo/templates/Bar/@global.latte",
				$context->parameters['appDir'] . "/Foo/templates/@global.latte",
				$context->parameters['appDir'] . "/templates/Foo/Bar/baz.latte",
				$context->parameters['appDir'] . "/templates/Foo/Bar.baz.latte",
				$context->parameters['appDir'] . "/templates/Foo/Bar/@global.latte",
				$context->parameters['appDir'] . "/templates/Foo/@global.latte",
				$context->parameters['appDir'] . "/templates/@global.latte",
				__DIR__ . "/Foo/templates/Bar/baz.latte",
				__DIR__ . "/Foo/templates/Bar.baz.latte",
				__DIR__ . "/Foo/templates/Bar/@global.latte",
				__DIR__ . "/Foo/templates/@global.latte",
				__DIR__ . "/templates/Foo/Bar/baz.latte",
				__DIR__ . "/templates/Foo/Bar.baz.latte",
				__DIR__ . "/templates/Foo/Bar/@global.latte",
				__DIR__ . "/templates/Foo/@global.latte",
				__DIR__ . "/templates/@global.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/Bar/baz.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/Bar.baz.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/Bar/@global.latte",
				$context->expand('%appDir%/Nella') . "/Foo/templates/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar/baz.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar.baz.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/Bar/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/Foo/@global.latte",
				$context->expand('%appDir%/Nella') . "/templates/@global.latte",
			)),
		);
	}

	/**
	 * @dataProvider dataFormatTemplatesFiles
	 */
	public function testFormatTemplatesFiles($presenter, $view, $eq)
	{
		$this->presenter->name = $presenter;
		$this->presenter->view = $view;

		Assert::equal($eq, $this->presenter->formatTemplateFiles(), "->formatTemplateFiles() $presenter:$view");
	}
}

id(new PresenterTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
