<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Models;

require_once __DIR__ . "/../bootstrap.php";

class ServiceTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Modles\Service */
	private $service;
	
	public function setUp()
	{
		$this->service = new ServiceMock(\Doctrine\ODM\MongoDB\Tests\Mocks\DocumentManagerMock::create());
	}
	
	public function testGetDocumentManager()
	{
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $this->service->getDocumentManager(), "->getDocumentManager() instance Doctrine DocumentManger");
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $this->service->documentManager, "->documentManager instance Doctrine DocumentManger");
	}
	
	public function testGetDocumentName()
	{
		
		$this->assertNull($this->service->getDocumentName(), "->getDocumentName() default is NULL");
		$this->assertNull($this->service->documentName, "->documentName default is NULL");
		
		$this->service = new ServiceMock(\Doctrine\ODM\MongoDB\Tests\Mocks\DocumentManagerMock::create(), 'Test');
		$this->assertEquals("Test", $this->service->getDocumentName(), "->getDocumentName() is 'Test'");
		$this->assertEquals("Test", $this->service->documentName, "->documentName is 'Test'");
	}
	
	public function testGetDocumentRepository()
	{
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentRepository', $this->service->getDocumentRepository('NellaTests\Models\DocumentMock'), "->getDocumentRepository() is instaceof Doctrine DocumentRepository");
		
		$this->service = new ServiceMock(\Doctrine\ODM\MongoDB\Tests\Mocks\DocumentManagerMock::create(), 'NellaTests\Models\DocumentMock');
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentRepository', $this->service->getDocumentRepository(), "->getDocumentRepository() is instaceof Doctrine DocumentRepository");
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentRepository', $this->service->documentRepository, "->documentRepository is instaceof Doctrine DocumentRepository");
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetDocumentRepositoryException()
	{
		$this->service->documentRepository;
	}
	
	public function testGetClassMetadata()
	{
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\Mapping\ClassMetadata', $this->service->getClassMetadata('NellaTests\Models\DocumentMock'), "->getClassMetadata() is instaceof Doctrine ClassMetadata");
		
		$this->service = new ServiceMock(\Doctrine\ODM\MongoDB\Tests\Mocks\DocumentManagerMock::create(), 'NellaTests\Models\DocumentMock');
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\Mapping\ClassMetadata', $this->service->getClassMetadata(), "->getClassMetadata() is instaceof Doctrine ClassMetadata");
		$this->assertInstanceOf('Doctrine\ODM\MongoDB\Mapping\ClassMetadata', $this->service->classMetadata, "->classMetadata is instaceof Doctrine ClassMetadata");
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetClassMetadataException()
	{
		$this->service->classMetadata;
	}
}

class ServiceMock extends \Nella\Models\Service { }
