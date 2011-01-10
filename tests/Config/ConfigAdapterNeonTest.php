<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Config;

use Nella\Config\ConfigAdapterNeon;

class ConfigAdapterNeonTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException LogicException
	 * 
	 */
	public function testInstanceException()
	{
		new ConfigAdapterNeon;
	}
	
	/**
	 * @covers Nella\Config\ConfigAdapterNeon::load
	 */
	public function testLoad()
	{
		$data = array(
			'bar' => array('test' => "foo"), 
			'foo' => array(
				'test4' => "bar", 
				'test' => "Europe/Prague", 
				'test2' => "%test%/Foo", 
				'test3' => 1, 
				'test5' => array(0 => "foo", 1 => "bar"), 
				'test6' => TRUE, 
			), 
			'Foo\Bar' => array(
				'test' => "Europe/Prague", 
				'test2' => "%test%/Foo", 
				'test3' => 128, 
				'test4' => "foo", 
				'test5' => array('foo' => "bar"), 
				'test6' => FALSE, 
			), 
		);
		
		$this->assertEquals($data, ConfigAdapterNeon::load(__DIR__ . "/ConfigAdapterNeonTest.neon", 'test'), "::load");
	}
	
	/**
	 * @covers Nella\Config\ConfigAdapterNeon::load
	 * @expectedException InvalidStateException
	 */
	public function testLoadException1()
	{
		ConfigAdapterNeon::load(__DIR__ . "/ConfigAdapterNeonTest.neon", "exception");
	}
	
	/**
	 * @covers Nella\Config\ConfigAdapterNeon::load
	 * @expectedException FileNotFoundException
	 */
	public function testLoadException2()
	{
		ConfigAdapterNeon::load(__DIR__ . "/exception.neon", 'test');
	}
}