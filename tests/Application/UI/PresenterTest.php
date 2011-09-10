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
		$this->context->params['namespaces'] = array('App', 'NellaTests\Application\UI', 'Nella');
		$this->context->params['templates'] = array(
			$this->context->params['appDir'],
			__DIR__,
			NELLA_FRAMEWORK_DIR,
		);
		$this->presenter = new Presenter\PresenterMock;
		$this->presenter->setContext($this->context);
	}

	public function dataFormmatLayoutTemplatesFiles()
	{
		$context = \Nette\Environment::getContext();
		return array(
			array('Foo', 'bar', array(
				$context->params['appDir'] . "/templates/Foo/@bar.latte",
				$context->params['appDir'] . "/templates/Foo.@bar.latte",
				$context->params['appDir'] . "/templates/@bar.latte",
				__DIR__ . "/templates/Foo/@bar.latte",
				__DIR__ . "/templates/Foo.@bar.latte",
				__DIR__ . "/templates/@bar.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/@bar.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo.@bar.latte",
				NELLA_FRAMEWORK_DIR . "/templates/@bar.latte",
			)),
			array('Foo:Bar', 'layout', array(
				$context->params['appDir'] . "/Foo/templates/Bar/@layout.latte",
				$context->params['appDir'] . "/Foo/templates/Bar.@layout.latte",
				$context->params['appDir'] . "/Foo/templates/@layout.latte",
				$context->params['appDir'] . "/templates/Foo/Bar/@layout.latte",
				$context->params['appDir'] . "/templates/Foo/Bar.@layout.latte",
				$context->params['appDir'] . "/templates/Foo/@layout.latte",
				$context->params['appDir'] . "/templates/@layout.latte",
				__DIR__ . "/Foo/templates/Bar/@layout.latte",
				__DIR__ . "/Foo/templates/Bar.@layout.latte",
				__DIR__ . "/Foo/templates/@layout.latte",
				__DIR__ . "/templates/Foo/Bar/@layout.latte",
				__DIR__ . "/templates/Foo/Bar.@layout.latte",
				__DIR__ . "/templates/Foo/@layout.latte",
				__DIR__ . "/templates/@layout.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar/@layout.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar.@layout.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/templates/@layout.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar/@layout.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar.@layout.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/@layout.latte",
				NELLA_FRAMEWORK_DIR . "/templates/@layout.latte",
			)),
		);
	}

	/**
	 * @dataProvider dataFormmatLayoutTemplatesFiles
	 */
	public function testFormmatLayoutTemplatesFiles($presenter, $layout, $eq)
	{
		$this->presenter->name = $presenter;
		$this->presenter->layout = $layout;
		$this->assertEquals(
			$eq,
			$this->presenter->formatLayoutTemplateFiles(),
			"->formatLayoutTemplateFiles() $presenter:@$layout"
		);
	}

	public function dataFormatTemplatesFiles()
	{
		$context = \Nette\Environment::getContext();
		return array(
			array('Foo', 'bar', array(
				$context->params['appDir'] . "/templates/Foo/bar.latte",
				$context->params['appDir'] . "/templates/Foo.bar.latte",
				$context->params['appDir'] . "/templates/Foo/@global.latte",
				$context->params['appDir'] . "/templates/@global.latte",
				__DIR__ . "/templates/Foo/bar.latte",
				__DIR__ . "/templates/Foo.bar.latte",
				__DIR__ . "/templates/Foo/@global.latte",
				__DIR__ . "/templates/@global.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/bar.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo.bar.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/@global.latte",
				NELLA_FRAMEWORK_DIR . "/templates/@global.latte",
			)),
			array('Foo:Bar', 'baz', array(
				$context->params['appDir'] . "/Foo/templates/Bar/baz.latte",
				$context->params['appDir'] . "/Foo/templates/Bar.baz.latte",
				$context->params['appDir'] . "/Foo/templates/Bar/@global.latte",
				$context->params['appDir'] . "/Foo/templates/@global.latte",
				$context->params['appDir'] . "/templates/Foo/Bar/baz.latte",
				$context->params['appDir'] . "/templates/Foo/Bar.baz.latte",
				$context->params['appDir'] . "/templates/Foo/Bar/@global.latte",
				$context->params['appDir'] . "/templates/Foo/@global.latte",
				$context->params['appDir'] . "/templates/@global.latte",
				__DIR__ . "/Foo/templates/Bar/baz.latte",
				__DIR__ . "/Foo/templates/Bar.baz.latte",
				__DIR__ . "/Foo/templates/Bar/@global.latte",
				__DIR__ . "/Foo/templates/@global.latte",
				__DIR__ . "/templates/Foo/Bar/baz.latte",
				__DIR__ . "/templates/Foo/Bar.baz.latte",
				__DIR__ . "/templates/Foo/Bar/@global.latte",
				__DIR__ . "/templates/Foo/@global.latte",
				__DIR__ . "/templates/@global.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar/baz.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar.baz.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/templates/Bar/@global.latte",
				NELLA_FRAMEWORK_DIR . "/Foo/templates/@global.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar/baz.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar.baz.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/Bar/@global.latte",
				NELLA_FRAMEWORK_DIR . "/templates/Foo/@global.latte",
				NELLA_FRAMEWORK_DIR . "/templates/@global.latte",
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

	public function testGlobalComponent()
	{
		$this->presenter->getContext()->getService('components')
			->addComponent('foo', function($parent, $name) { return "bar"; });

		$this->assertEquals("bar", $this->presenter->createComponentMock('foo'));
		$this->assertNull($this->presenter->createComponentMock('bar'));
	}
}

namespace NellaTests\Application\UI\Presenter;

class PresenterMock extends \Nella\Application\UI\Presenter
{
	public function createComponentMock($name)
	{
		return $this->createComponent($name);
	}

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