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

use Nella\Models\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	/** @var \Nella\Modles\Service */
	private $service;
	
	public function setUp()
	{
		$this->em = \Doctrine\Tests\Mocks\EntityManagerMock::create(new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver));
		$this->service = new Service($this->em);
	}
	
	public function testGetEntityManager()
	{
		$this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->service->getEntityManager(), "->getEntityManager() instance Doctrine EntityManger");
		$this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->service->entityManager, "->entityManager instance Doctrine EntityManger");
	}
	
	public function testGetEntityClass()
	{
		
		$this->assertNull($this->service->getEntityClass(), "->getEntityClass() default is NULL");
		$this->assertNull($this->service->entityClass, "->entityClass default is NULL");
		
		$this->service = new Service($this->em, 'Test');
		$this->assertEquals("Test", $this->service->getEntityClass(), "->getEntityName() is 'Test'");
		$this->assertEquals("Test", $this->service->entityClass, "->entityClass is 'Test'");
	}
	
	public function testGetRepository()
	{
		$this->assertInstanceOf('Doctrine\ORM\EntityRepository', $this->service->getRepository('NellaTests\Models\EntityMock'), "->getRepository() is instaceof Doctrine EntityRepository");
		
		$this->service = new Service($this->em, 'NellaTests\Models\EntityMock');
		$this->assertInstanceOf('Doctrine\ORM\EntityRepository', $this->service->getRepository(), "->getRepository() is instaceof Doctrine EntityRepository");
		$this->assertInstanceOf('Doctrine\ORM\EntityRepository', $this->service->repository, "->repository is instaceof Doctrine EntityRepository");
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetRepositoryException()
	{
		$this->service->repository;
	}
	
	public function testGetClassMetadata()
	{
		$this->assertInstanceOf('Doctrine\ORM\Mapping\ClassMetadata', $this->service->getClassMetadata('NellaTests\Models\EntityMock'), "->getClassMetadata() is instaceof Doctrine ClassMetadata");
		
		$this->service = new Service($this->em, 'NellaTests\Models\EntityMock');
		$this->assertInstanceOf('Doctrine\ORM\Mapping\ClassMetadata', $this->service->getClassMetadata(), "->getClassMetadata() is instaceof Doctrine ClassMetadata");
		$this->assertInstanceOf('Doctrine\ORM\Mapping\ClassMetadata', $this->service->classMetadata, "->classMetadata is instaceof Doctrine ClassMetadata");
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetClassMetadataException()
	{
		$this->service->classMetadata;
	}
}
