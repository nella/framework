<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application\UI;

class PresenterTest extends \Nella\Testing\TestCase
{
	/** @var PresenterMock */
	private $presenter;

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

		$this->presenter = new Presenter\PresenterMock($context);
	}

	public function dataFormatLayoutTemplatesFiles()
	{
		$context = $this->getContext();
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

		$this->assertEquals($eq, $this->presenter->formatLayoutTemplateFiles(), "->formatLayoutTemplateFiles() $presenter:@$layout");
	}

	public function dataFormatTemplatesFiles()
	{
		$context = $this->getContext();
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

		$this->assertEquals($eq, $this->presenter->formatTemplateFiles(), "->formatTemplateFiles() $presenter:$view");
	}
}

namespace NellaTests\Application\UI\Presenter;

class PresenterMock extends \Nella\Application\UI\Presenter
{
	/**
	 * @param string
	 * @return \Nette\Application\UI\Presetner
	 */
	public function setName($name)
	{
		$ref = new \Nette\Reflection\Property('Nette\ComponentModel\Component', 'name');
		$ref->setAccessible(TRUE);
		$ref->setValue($this, $name);
		$ref->setAccessible(FALSE);
		return $this;
	}
}