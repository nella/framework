<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application;

class PresenterFactoryTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Application\PresenterFactory */
	private $loader;

	public function setup()
	{
		parent::setup();
		$this->loader = new \Nella\Application\PresenterFactory($this->getContext());
		$this->loader->addNamespace('Nella');
	}

	public function dataFormatPresenterClass()
	{
		return array(
			array('Foo', 'App\FooPresenter'),
			array('Foo:Bar', 'App\Foo\BarPresenter'),
			array('Foo:Bar:Baz', 'App\Foo\Bar\BazPresenter'),
			array('Foo', 'Nella\FooPresenter', 'Nella'),
			array('Foo:Bar', 'Nella\Foo\BarPresenter', 'Nella'),
			array('Foo:Bar:Baz', 'Nella\Foo\Bar\BazPresenter', 'Nella'),
		);
	}

	/**
	 * @dataProvider dataFormatPresenterClass
	 */
	public function testFormatPresenterClass($presenter, $class, $namespace = 'App')
	{
		$this->assertEquals($class, $this->loader->formatPresenterClass($presenter, $namespace), "->formatPresenterClass('$presenter')");
	}

	public function dataUnformatPresenterClass()
	{
		return array(
			array('App\FooPresenter', 'Foo'),
			array('App\Foo\BarPresenter', 'Foo:Bar'),
			array('App\Foo\Bar\BazPresenter', 'Foo:Bar:Baz'),
			array('Nella\FooPresenter', 'Foo'),
			array('Nella\Foo\BarPresenter', 'Foo:Bar'),
			array('Nella\Foo\Bar\BazPresenter', 'Foo:Bar:Baz'),
		);
	}

	/**
	 * @dataProvider dataUnformatPresenterClass
	 */
	public function testUnformatPresenterClass($class, $presenter)
	{
		$this->assertEquals($presenter, $this->loader->unformatPresenterClass($class), "->unformatPresenterClass('$class')");
	}

	public function dataGetPresenterClass()
	{
		return array(
			array('Foo', 'NellaTests\Application\PresenterFactory\FooPresenter'),
			array('Bar:Foo', 'NellaTests\Application\PresenterFactory\Bar\FooPresenter'),
			array('My', 'NellaTests\Application\PresenterFactoryTest\MyPresenter'),
			array('Foo:My', 'NellaTests\Application\PresenterFactoryTest\Foo\MyPresenter'),
		);
	}

	/**
	 * @dataProvider dataGetPresenterClass
	 */
	public function testGetPresenterClass($presenter, $class)
	{
		$this->loader->addNamespace('NellaTests\Application\PresenterFactory', 1);
		$this->loader->addNamespace('NellaTests\Application\PresenterFactoryTest', 1);
		$this->assertEquals($class, $this->loader->getPresenterClass($presenter), "->getPresenterClass('$presenter')");
	}

	/**
	 * @expectedException Nette\Application\InvalidPresenterException
	 */
	public function testGetPresenterClassException1()
	{
		$name = NULL;
		$this->loader->getPresenterClass($name);
	}

	/**
	 * @expectedException Nette\Application\InvalidPresenterException
	 */
	public function testGetPresenterClassException2()
	{
		$name = 'Bar';
		$this->loader->getPresenterClass($name);
	}

	/**
	 * @expectedException Nette\Application\InvalidPresenterException
	 */
	public function testGetPresenterClassException3()
	{
		$name = 'Baz';
		$this->loader->getPresenterClass($name);
	}

	/**
	 * @expectedException Nette\Application\InvalidPresenterException
	 */
	public function testGetPresenterClassException4()
	{
		$name = 'Bar';
		$this->loader->getPresenterClass($name);
	}
}

namespace NellaTests\Application\PresenterFactory;

class FooPresenter extends \Nette\Application\UI\Presenter { }

namespace NellaTests\Application\PresenterFactory\Bar;

class FooPresenter extends \Nette\Application\UI\Presenter { }

namespace NellaTests\Application\PresenterFactoryTest;

class MyPresenter extends \Nette\Application\UI\Presenter { }

namespace NellaTests\Application\PresenterFactoryTest\Foo;

class MyPresenter extends \Nette\Application\UI\Presenter { }