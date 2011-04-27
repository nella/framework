<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\DI;

require_once __DIR__ . "/../bootstrap.php";

class ContextTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\DI\Context */
	private $context;

	public function setUp()
	{
		$this->context = new \Nella\DI\Context;
	}

	public function testEnvironment()
	{
		$this->assertNull($this->context->environment, "default environment name not set");
		$this->context->environment = 'foo';
		$this->assertEquals('foo', $this->context->environment, "->environment is 'foo'");
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testEnvironmentException()
	{
		$this->context->freeze();
		$this->context->environment = 'foo';
	}

	public function testParameters()
	{
		$this->context->setParameter('foo', "Bar");
		$this->assertTrue($this->context->hasParameter('foo'), "->hasParamter('foo') true after parameter set");
		$this->assertEquals("Bar", $this->context->getParameter('foo'), "->getParameter('foo') equals 'bar'");
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testParamterException1()
	{
		$this->context->setParameter(NULL, "Foo");
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testParamterException2()
	{
		$this->context->setParameter("...", "Foo");
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testParamterFrozenException()
	{
		$this->context->freeze();
		$this->context->setParameter('foo', "Bar");
	}

	public function testParametersExpandVar()
	{
		$this->context->setParameter('foo', "test");
		$this->context->setParameter('bar', "%foo%");
		$this->assertEquals("test", $this->context->getParameter('foo'), "->getParameter('foo') equals 'test'");
		$this->assertEquals("test", $this->context->getParameter('bar'), "->getParameter('bar') equals 'test' from foo paramter");
	}

	public function testParametersExpandService()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->setParameter('foo', "@Foo");
		$this->assertTrue($this->context->hasParameter('foo'), "->hasParamter('foo') true after parameter set");
		$this->assertEquals(new Foo, $this->context->getParameter('foo'), "->getParameter('foo') equals Foo instance");
	}

	public function testBasicInstance()
	{
		$this->context->addService('Test', new Foo);

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}

	public function testBasicClass()
	{
		$this->context->addService('Test', 'NellaTests\DI\Foo');

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}

	public function testBasicFactory()
	{
		$this->context->addService('Test', function() { return new Foo; });

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}

	public function testBasicConstructorInjection()
	{
		$this->context->addService('Test', 'NellaTests\DI\Foo', TRUE, array('arguments' => array("Test")));

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertEquals('Test', $this->context->getService('Test')->bar, "service->bar equals 'Test'");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}

	public function testBasicFacotryInjection()
	{
		$this->context->addService('Test', function($bar) { return new Foo($bar); }, TRUE, array('arguments' => array("Test")));

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertEquals('Test', $this->context->getService('Test')->bar, "service->bar equals 'Test'");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}

	public function testBasicMethodInjectionClass()
	{
		$this->context->addService(
			'Test',
			'NellaTests\DI\Foo',
			TRUE,
			array('methods' => array(
					array('method' => "setBar", 'arguments' => array("Test")),
				)
			)
		);

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
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
			array('methods' => array(
					array('method' => "setBar", 'arguments' => array("Test")),
				)
			)
		);

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertEquals('Test', $this->context->getService('Test')->bar, "service->bar equals 'Test'");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}

	public function testServiceConstructorInjection()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addService('Test', 'NellaTests\DI\Foo', TRUE, array('arguments' => array("@Foo")));

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertSame($this->context->getService('Foo'), $this->context->getService('Test')->bar, "service->bar is same as Foo service");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}

	public function testServiceFacotryInjection()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addService('Test', function($bar) { return new Foo($bar); }, TRUE, array('arguments' => array("@Foo")));

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
		$this->assertSame($this->context->getService('Foo'), $this->context->getService('Test')->bar, "service->bar is same as Foo service");
		$this->context->removeService('Test');
		$this->assertFalse($this->context->hasService('Test'), "has not service Test");
	}

	public function testServiceMethodInjectionClass()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addService(
			'Test',
			'NellaTests\DI\Foo',
			TRUE,
			array('methods' => array(
					array('method' => "setBar", 'arguments' => array("@Foo")),
				)
			)
		);

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
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
			array('methods' => array(
					array('method' => "setBar", 'arguments' => array("@Foo")),
				)
			)
		);

		$this->assertTrue($this->context->hasService('Test'), "has service Test");
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context->getService('Test'), "get service Test (Foo instance)");
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
		$this->assertInstanceOf('NellaTests\DI\Foo', $this->context['Foo'], "is Foo service instance Foo class");
		unset($this->context['Foo']);
		$this->assertFalse(isset($this->context['Foo']), "is not set Foo service");
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testAddServiceFrozenException()
	{
		$this->context->freeze();
		$this->context->addService('Test', new Foo);
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testAddAliasFrozenException()
	{
		$this->context->freeze();
		$this->context->addAlias('Test', 'Test');
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testRemoveServiceFrozenException()
	{
		$this->context->addService('Test', new Foo);
		$this->context->freeze();
		$this->context->removeService('Test');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testAddServiceBadNameException()
	{
		$this->context->addService('', new Foo);
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testAddServiceBadServiceException()
	{
		$this->context->addService('Test', NULL);
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testAddServiceSignletonInstanceException()
	{
		$this->context->addService('Test', new Foo, FALSE);
	}

	/**
	 * @expectedException Nette\DI\AmbiguousServiceException
	 */
	public function testAddServiceRegisteredException()
	{
		$this->context->addService('Test', new Foo);
		$this->context->addService('Test', new Foo);
	}

	/**
	 * @expectedException Nette\DI\AmbiguousServiceException
	 */
	public function testAddServiceRegisteredAliasException()
	{
		$this->context->addService('Foo', function () { return new Foo; });
		$this->context->addAlias('Test', "Foo");
		$this->context->addService('Test', new Foo);
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testAddAliasBadNameException()
	{
		$this->context->addAlias('', 'Test');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testAddAliasBadServiceException()
	{
		$this->context->addAlias('Test', '');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testAddAliasNotExistServiceException()
	{
		$this->context->addAlias('Test', 'Test');
	}

	/**
	 * @expectedException Nette\DI\AmbiguousServiceException
	 */
	public function testAddAliasNotExistingException()
	{
		$this->context->addService('Test', new Foo);
		$this->context->addAlias('Foo', 'Test');
		$this->context->addAlias('Foo', 'Test');
	}

	/**
	 * @expectedException Nette\DI\AmbiguousServiceException
	 */
	public function testAddAliasNotExistingServiceException()
	{
		$this->context->addService('Test', new Foo);
		$this->context->addAlias('Test', 'Test');
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testGetServiceNonExistException()
	{
		$this->context->getService('Test');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testGetServiceInstanceOptionsException()
	{
		$this->context->addService('Test', new Foo);
		$this->context->getService('Test', array("foo"));
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testRemoveServiceBadNameException()
	{
		$this->context->removeService('');
	}

	public function testGetConstantParameter()
	{
		$this->assertEquals(APP_DIR, $this->context->getParameter('appDir'), '->getParameter("appDir") equals APP_DIR');
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testGetNonExistParameter()
	{
		$this->context->getParameter('iLoveNetteFrameworkAndNetteFrameworkCreator!Really');
	}

	public function testGetFactory()
	{
		$this->context->addService('Test', 'NellaTests\DI\Foo');
		$factory = $this->context->getFactory('Test');

		$this->assertInstanceOf('Nella\DI\IServiceFactory', $factory, "->getFactory('Test') instance of IServiceFactory");
		$this->assertInstanceOf('NellaTests\DI\Foo', $factory->getInstance(), "\$factory->getInstance() instance of defined service");
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testGetNonExistFactoryException()
	{
		$this->context->getFactory('Test');
	}

	public function testSetFactory()
	{
		$factory = new \Nella\DI\ServiceFactory($this->context, 'Test');
		$this->context->addFactory($factory);

		$this->assertSame($factory, $this->context->getFactory('Test'));
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testSetFrozenFactoryException()
	{
		$this->context->freeze();

		$factory = new \Nella\DI\ServiceFactory($this->context, 'Test');
		$this->context->addFactory($factory);
	}

	/**
	 * @expectedException Nette\DI\AmbiguousServiceException
	 */
	public function testSetExistingFactoryException()
	{
		$this->context->addService('Test', new Foo);

		$factory = new \Nella\DI\ServiceFactory($this->context, 'Test');
		$this->context->addFactory($factory);
	}

	/**
	 * @expectedException Nette\DI\AmbiguousServiceException
	 */
	public function testSetExistingAliasFactoryException()
	{
		$this->context->addService('Foo', new Foo);
		$this->context->addAlias('Test', 'Foo');

		$factory = new \Nella\DI\ServiceFactory($this->context, 'Test');
		$this->context->addFactory($factory);
	}
}
