<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\DependencyInjection;

require_once __DIR__ . "/../bootstrap.php";

class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
	/** @var ServiceFactoryMock */
	private $factory;
	
	public function setUp()
	{
		parent::setUp();
		$this->factory = new ServiceFactoryMock(new \Nella\DependencyInjection\Context, 'foo');
	}

	public function testSettersAndGetters()
	{
		$this->assertInstanceOf('Nella\DependencyInjection\IContext', $this->factory->context, 
			"->context equals IContext instance"
		);
		
		$this->assertEquals("foo", $this->factory->name, "->name equals 'foo'");
		
		$this->factory->class = 'NellaTests\DependencyInjection\Foo';
		$this->assertEquals('NellaTests\DependencyInjection\Foo', $this->factory->class, 
			"->class equals previous set class"
		);
		
		$this->factory->arguments = array('foo', 'bar');
		$this->assertEquals(array('foo', 'bar'), $this->factory->arguments, 
			"->arguments equals previous set arguments"
		);
		
		$this->factory->factory = 'NellaTests\DependencyInjection\Foo::create';
		$this->assertInstanceOf('Nette\Callback', $this->factory->factory, 
			"->factory iunstance of Nette\\Callback after set string callback"
		);
		
		$methods = array(
			
		);
		$this->factory->methods = $methods;
		$this->assertEquals($methods, $this->factory->methods, "->methods equals previous set methods");
		
		$this->factory->singleton = FALSE;
		$this->assertFalse($this->factory->singleton, "->singleton false previous set");
	}
	
	public function testAddersAndGetters()
	{
		$this->factory->addArgument("Foo");
		$this->assertEquals(array("Foo"), $this->factory->arguments, 
			"->arguments equals previous added argument"
		);
		$this->factory->addMethod("bar", array("test"));
		$this->assertEquals(array(array('method' => "bar", 'arguments' => array("test"))), $this->factory->methods, 
			"->methods equals previous added method
		");
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetClassException()
	{
		$this->factory->singleton = FALSE;
		$this->factory->class = new Foo;
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetFactoryException()
	{
		$this->factory->factory = NULL;
	}
	
	public function testCreateInstance1()
	{
		$this->factory->setClass('NellaTests\DependencyInjection\Foo');
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->factory->createInstanceMock());
	}
	
	public function testCreateInstance2()
	{
		$instance = new Foo;
		$this->factory->setClass($instance);
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->factory->createInstanceMock());
		$this->assertSame($instance, $this->factory->createInstanceMock());
	}
	
	public function testCreateInstance3()
	{
		$this->factory->setFactory(function () { return new Foo; });
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->factory->createInstanceMock());
	}
	
	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testCreateInstanceException1()
	{
		$this->factory->setClass(\Nette\Utils\Strings::random() . "VrtakSuperUperDuperCoolClass");
		$this->factory->createInstanceMock();
	}
	
	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testCreateInstanceException2()
	{
		$this->factory->createInstanceMock();
	}
	
	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testCreateInstanceException3()
	{
		$this->factory->class = new Foo;
		$this->factory->singleton = FALSE;
		$this->factory->createInstanceMock();
	}
	
	public function testGetInstance()
	{
		$this->factory->class = new Foo;
		$this->factory->addMethod('setBar', array(new Foo));
		$instance = $this->factory->getInstance();
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $instance, 
			"->getInstance() is instance of test object"
		);
		
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $instance->bar, 
			"->getInstance()->bar is instance of test object"
		);
	}
	
	public function testFactoryAsConfigurator()
	{
		$this->factory->class = new Foo;
		$this->factory->factory = function (Foo $instance) { $instance->setBar(new Foo); };
		$instance = $this->factory->getInstance();
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $instance, 
			"->getInstance() is instance of test object"
		);
		
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $instance->bar, 
			"->getInstance()->bar is instance of test object"
		);
	}
}

class Foo extends \Nette\Object
{
	public $bar;
	
	public function __construct($bar = NULL)
	{
		$this->bar = $bar;
	}
	
	public function setBar($bar)
	{
		$this->bar = $bar;
	}
	
	public static function create(Foo $foo)
	{
		return new static($foo);
	}
}
