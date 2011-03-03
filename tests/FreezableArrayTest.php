<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests;

require_once __DIR__ . "/bootstrap.php";

class FreezableArrayTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\FreezableArray */
	private $arr;
	
	public function setUp()
	{
		$this->arr = new \Nella\FreezableArray;
		$this->arr['foo'] = "bar";
	}
	
	public function testGet()
	{
		$this->assertTrue(isset($this->arr['foo']), "isset(\$arr['foo']) if defined offset");
		$this->assertEquals("bar", $this->arr['foo'], "\$arr['foo'] equals 'bar'");
	}
	
	public function testSet()
	{
		$this->arr['bar'] = "foo";
		$this->assertTrue(isset($this->arr['bar']), "isset(\$arr['bar']) if defined offset");
		$this->assertEquals("foo", $this->arr['bar'], "\$arr['bar'] equals 'foo'");
	}
	
	public function testUnset()
	{
		unset($this->arr['foo']);
		$this->assertFalse(isset($this->arr['foo']), "isset(\$arr['foo']) if defined offset");
	}
	
	public function testIterator()
	{
		$iterator = $this->arr->getIterator();
		$this->assertInstanceOf("ArrayIterator", $iterator);
		$this->assertEquals("bar", $iterator->current(), "->current equals 'bar'");
	}
	
	/**
	 * @expectedException MemberAccessException
	 */
	public function testUndefined()
	{
		$this->arr['bar'];
	}
	
	/**
	 * @expectedException InvalidStateException
	 */
	public function testFrozenSet()
	{
		$this->arr->getIterator();
		$this->arr['bar'] = "foo";
	}
	
	/**
	 * @expectedException InvalidStateException
	 */
	public function testFrozenUnset()
	{
		$this->arr->getIterator();
		unset($this->arr['foo']);
	}
}
