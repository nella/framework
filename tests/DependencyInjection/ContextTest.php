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

class ContextTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\DependencyInjection\Context */
	private $context;
	
	public function setUp()
	{
		$this->context = new \Nella\DependencyInjection\Context;
	}
	
	public function testBasicInstance()
	{
		$this->context->addService('Test', new Foo);
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testBasicClass()
	{
		$this->context->addService('Test', 'NellaTests\DependencyInjection\Foo');
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testBasicFactory()
	{
		$this->context->addService('Test', function() { return new Foo; });
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testBasicConstructorInjection()
	{
		$this->context->addService('Test', 'NellaTests\DependencyInjection\Foo', TRUE, array('arguments' => array("Test")));
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertEquals('Test', $this->context->getService('Test')->bar, "service->bar equals 'Test'");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testBasicFacotryInjection()
	{
		$this->context->addService('Test', function($bar) { return new Foo($bar); }, TRUE, array('arguments' => array("Test")));
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertEquals('Test', $this->context->getService('Test')->bar, "service->bar equals 'Test'");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testBasicMethodInjectionClass()
	{
		$this->context->addService(
			'Test', 
			'NellaTests\DependencyInjection\Foo', 
			TRUE, 
			array('callMethods' => array(
				'setBar' => array("Test")
				)
			)
		);
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertEquals('Test', $this->context->getService('Test')->bar, "service->bar equals 'Test'");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testBasicMethodInjectionFactory()
	{
		$this->context->addService(
			'Test', 
			function() { 
				return new Foo; 
			}, 
			TRUE, 
			array('callMethods' => array(
				'setBar' => array("Test")
				)
			)
		);
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertEquals('Test', $this->context->getService('Test')->bar, "service->bar equals 'Test'");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testServiceConstructorInjection()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addService('Test', 'NellaTests\DependencyInjection\Foo', TRUE, array('arguments' => array("@Foo")));
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertSame($this->context->getService('Foo'), $this->context->getService('Test')->bar, "service->bar is same as Foo service");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testServiceFacotryInjection()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addService('Test', function($bar) { return new Foo($bar); }, TRUE, array('arguments' => array("@Foo")));
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertSame($this->context->getService('Foo'), $this->context->getService('Test')->bar, "service->bar is same as Foo service");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testServiceMethodInjectionClass()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addService(
			'Test', 
			'NellaTests\DependencyInjection\Foo', 
			TRUE, 
			array('callMethods' => array(
				'setBar' => array("@Foo")
				)
			)
		);
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertSame($this->context->getService('Foo'), $this->context->getService('Test')->bar, "service->bar is same as Foo service");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testServiceMethodInjectionFactory()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addService(
			'Test', 
			function() { 
				return new Foo; 
			}, 
			TRUE, 
			array('callMethods' => array(
				'setBar' => array("@Foo")
				)
			)
		);
		
		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertSame($this->context->getService('Foo'), $this->context->getService('Test')->bar, "service->bar is same as Foo service");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}
	
	public function testAliases()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addAlias('Bar', 'Foo');
		$this->context->addAlias('Baz', 'Foo');
		
		$this->assertTrue($this->context->hasService('Bar'), "has service Bar");
		$this->assertSame($this->context->getService('Foo'), $this->context->getService('Bar'), "get service Bar is as Foo service");
		$this->assertTrue($this->context->hasService('Baz'), "has service Baz");
		$this->assertSame($this->context->getService('Foo'), $this->context->getService('Baz'), "get service Baz is as Foo service");
	}
	
	public function testArrayAccess()
	{
		$this->context['Foo'] = new Foo;
		$this->assertTrue(isset($this->context['Foo']), "is set Foo service");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $this->context['Foo'], "is Foo service instance Foo class");
		unset($this->context['Foo']);
		$this->assertFalse(isset($this->context['Foo']), "is not set Foo service");
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
