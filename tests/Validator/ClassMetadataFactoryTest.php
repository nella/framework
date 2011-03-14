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

class ClassMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Validator\ClassMetadataFactory */
	private $factory;
	
	public function setUp()
	{
		$this->factory = new \Nella\Validator\ClassMetadataFactory;
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetInvalidClassMetadataException()
	{
		$this->factory->getClassMetadata('Test');
	}
	
	public function testGetClassMetadata()
	{
		$this->assertInstanceOf('Nella\Validator\ClassMetadata', $this->factory->getClassMetadata('NellaTests\Validator\Foo'), 
			"->getClassMetadata('..') instance of ClassMetadata");
		
		$this->assertInstanceOf('Nella\Validator\ClassMetadata', $this->factory->getClassMetadata('NellaTests\Validator\Foo'), 
			"->getClassMetadata('..') - from registry - instance of ClassMetadata");
	}
}