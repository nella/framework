<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\DependencyInjection;

use Nette\Environment, 
	Nette\Config\Config;

require_once __DIR__ . "/../bootstrap.php";

class ContextBuilderTest extends \PHPUnit_Framework_TestCase
{
	/** @var ContextBuilderMock */
	private $builder;
	
	public function setUp()
	{
		parent::setUp();
		$this->builder = new ContextBuilderMock;
		Environment::setConfigurator($this->builder);
	}
	
	public function testSetContextClass()
	{
		$class = 'Nella\DependencyInjection\Context';
		$this->builder->setContextClass($class);
		$this->assertInstanceOf($class, $this->builder->getContext());
	}
	
	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testSetContextClassException1()
	{
		$this->builder->setContextClass(NULL);
	}
	
	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testSetContextClassException2()
	{
		$this->builder->setContextClass(get_called_class());
	}
	
	public function testDetectEnvironment()
	{
		$this->builder->loadEnvironmentNameMock("foo");
		$this->assertEquals("foo", Environment::getVariable('environment'), 
			"Environment::getVariable environment name equals 'foo"
		);
		$this->assertEquals("foo", $this->builder->context->environment, '$context->environment equals "foo"');
	}
	
	public function testLoadIni()
	{
		$data = array(
			'include_path' => get_include_path(), 
			'iconv.internal_encoding' => iconv_get_encoding(), 
			'mbstring.internal_encoding' => mb_internal_encoding(), 
			'date' => array('timezone' => "Europe/Prague"), 
			'error_reporting' => E_ALL | E_STRICT, 
			'ignore_user_abort' => ignore_user_abort(), 
			'max_execution_time' => 0, 
		);
		$this->builder->loadIniMock(new Config($data));
		$this->assertEquals($data['date']['timezone'], date_default_timezone_get());
		
	}
	
	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testLoadIniException()
	{
		$data = array(
			'xxx' => (object) array('xxx'), 
		);
		$this->builder->loadIniMock(new Config($data));
		
	}
	
	public function testLoadParametersVariables()
	{
		$data = array('variables' => array('foo' => "Bar"));
		$this->builder->loadParametersMock(new Config($data));
		$this->assertTrue($this->builder->context->hasParameter('foo'), "exist variable foo");
		$this->assertEquals("Bar", $this->builder->context->getParameter('foo'), "variable foo equals 'Bar'");
		$this->assertEquals("Bar", Environment::getVariable('foo'), "variable foo equals 'Bar'");
	}
	
	public function testLoadParametersNormal()
	{
		$data = array(
			'foo' => "Bar", 
			'bar' => array('baz' => array('test' => "Test"), 'xxx' => "xXx"), 
		);
		
		$this->builder->loadParametersMock(new Config($data));
		$this->assertTrue($this->builder->context->hasParameter('foo'), "exist config foo");
		$this->assertEquals("Bar", $this->builder->context->getParameter('foo'), "config foo equals 'Bar'");
		
		$this->assertTrue($this->builder->context->hasParameter('bar'), "exist config bar");
		$this->assertEquals($data['bar'], $this->builder->context->getParameter('bar'), "config bar equals array");
		$data = $this->builder->context->getParameter('bar');
		$this->assertInternalType('array', $data['baz'], "config bar equals array");
		
		$this->assertFalse($this->builder->context->hasParameter('baz'), "not exist confg or variable baz");
	}
}
