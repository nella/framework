<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application\UI;

class ComponentContainerTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Application\UI\ComponentContainer */
	private $container;

	public function setup()
	{
		parent::setup();
		$this->container = new \Nella\Application\UI\ComponentContainer;
	}

	public function testGetComponentEmpty()
	{
		$this->assertFalse($this->container->hasComponent('foo'), 'default container is empty');
	}

	public function testGetComponent()
	{
		$this->assertFalse($this->container->hasComponent('baz'), 'default container is empty');

		$presenter = new ComponentContainer\PresenterMock;
		$control = new ComponentContainer\ControlMock($presenter, 'foo');
		$this->container->addComponent('foo', $control);
		$this->assertTrue($this->container->hasComponent('foo'), "added component 'foo'");
		$component = $this->container->getComponent('foo');
		$this->assertInstanceOf(
			'Nette\ComponentModel\IComponent',
			$component,
			"->getComponent('foo') return valid Nette IComponent"
		);
		$this->assertSame($control, $component, "->getComponent('foo') return valid component");

		$this->container->addComponent('bar', function($parent, $name) use($control) { return $control; });
		$this->assertTrue($this->container->hasComponent('bar'), "added component 'bar'");
		$component = $this->container->getComponent('bar');
		$this->assertInstanceOf(
			'Nette\ComponentModel\IComponent',
			$component,
			"->getComponent('bar') return valid Nette IComponent"
		);
		$this->assertSame($control, $component, "->getComponent('bar') return valid component");
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testGetComponentNotExist()
	{
		$this->container->getComponent('test');
	}
}

namespace NellaTests\Application\UI\ComponentContainer;

class ControlMock extends \Nella\Application\UI\Control { }

class PresenterMock extends \Nella\Application\UI\Presenter { }