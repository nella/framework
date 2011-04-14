<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Validator;

require_once __DIR__ . "/../bootstrap.php";

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Validator\ClassMetadata */
	private $metadata;
	
	public function setUp()
	{
		$this->metadata = new \Nella\Validator\ClassMetadata('NellaTests\Validator\Bar');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidClassMetadata()
	{
		new \Nella\Validator\ClassMetadata('Test');
	}
	
	public function testGetName()
	{
		$this->assertEquals('NellaTests\Validator\Bar', $this->metadata->getName(), 
			"->getName() equals metadata class name");
		$this->assertEquals('NellaTests\Validator\Bar', $this->metadata->name, 
			"->name equals metadata class name");
	}
	
	public function testGetParent()
	{
		$this->assertEquals('NellaTests\Validator\Foo', $this->metadata->getParent(), 
			"->getParent() equals metadata parent class name");
		$this->assertEquals('NellaTests\Validator\Foo', $this->metadata->parent, 
			"->parent equals metadata parent class name");
	}
	
	public function testGetParentWithoutParent()
	{
		$this->metadata = new \Nella\Validator\ClassMetadata('NellaTests\Validator\Foo');
		$this->assertNull($this->metadata->getParent(), 
			"->getParent() equals metadata parent class name");
		$this->assertNull($this->metadata->parent, 
			"->parent equals metadata parent class name");
	}
	
	public function testGetClassReflection()
	{
		$this->assertInstanceOf('Nette\Reflection\ClassType', $this->metadata->getClassReflection(), 
			"->getReflection() instance of Nette class reflection object");
		$this->assertInstanceOf('Nette\Reflection\ClassType', $this->metadata->classReflection, 
			"->reflection instance of Nette class reflection object");
	}
	
	public function testRule()
	{
		$this->metadata->addRule('test', "foo");
		$this->metadata->addRule('test', "bar");
		$rules = $this->metadata->rules;
		$this->assertEquals('test', current(array_keys($rules)), "test name");
		$this->assertEquals("foo", $rules['test'][0][0], "test rule");
		$this->assertEquals("bar", $rules['test'][1][0], "test rule");
		
	}
	
	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testRuleExistingRule()
	{
		$this->metadata->addRule('test', "foo");
		$this->metadata->addRule('test', "foo");
	}
}

class Foo
{
	/**
	 * @validate(url,minlength=20)
	 * @var mixed
	 */
	private $foo;
}

class Bar extends Foo
{
	/**
	 * @var mixed
	 */
	private $bar;
}
