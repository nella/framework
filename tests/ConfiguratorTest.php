<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests;

require_once __DIR__ . "/bootstrap.php";

use Nette\Environment;

class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Configurator */
	private $configurator;
	
	public function setUp()
	{
		$this->configurator = new \Nella\Configurator;
	}
	
	public function testCreateContext()
	{
		$this->assertInstanceOf('Nella\DependencyInjection\Context', $this->configurator->createContext(), "is Nella Context instance");
	}
	
	public function testLoadConfig()
	{
		$config = new \Nette\Config\Config(array(
			'service' => array(
				'NellaTests\DependencyInjection\Foo' => array(
					'class' => 'NellaTests\DependencyInjection\Foo', 
					'argument' => array("Test"), 
					'alias' => array("Baz")
				), 
				'Bar' => array(
					'factory' => 'NellaTests\DependencyInjection\Foo::create', 
					'autowire' => TRUE, 
				), 
			), 
		));
		
		$reflection = \Nette\Reflection\ClassReflection::from('Nette\Environment')->getProperty('context');
		$reflection->setAccessible(TRUE);
		$reflection->setValue($this->configurator->createContext());
		$reflection->setAccessible(FALSE);
		
		$this->configurator->loadConfig($config);
		$context = \Nette\Environment::getContext();
		
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $context->getService('NellaTests\DependencyInjection\Foo'), "get NellaTests\\DependencyInjection\\Foo service");
		$this->assertSame($context->getService('NellaTests\DependencyInjection\Foo'), $context->getService('Baz'), "get Baz service");
		$this->assertEquals("Test", $context->getService('Baz')->bar);
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $context->getService('Bar'), "get Bar service");
		$this->assertInstanceOf('NellaTests\DependencyInjection\Foo', $context->getService('Bar'), "service->bar instance");
		$this->assertSame($context->getService('NellaTests\DependencyInjection\Foo'), $context->getService('Bar')->bar, "service->bar is NellaTests\\DependencyInjection\\Foo service instance");
	}
}
