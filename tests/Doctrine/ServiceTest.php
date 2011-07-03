<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Doctrine;

use Nella\Doctrine\Service;

require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . "/../Mocks/EntityManagerMock.php";

class ServiceTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Doctrine\Service */
	private $service;

	public function setup()
	{
		$container = new \Nette\DI\Container;
		$container->params = array('productionMode' => FALSE, 'appDir' => __DIR__ . "/..");
		$container->addService('entityManager', \Doctrine\Tests\Mocks\EntityManagerMock::create(
			new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver)
		));
		$container = new \Nella\Doctrine\Container($container);
		$this->service = new Service($container, 'NellaTests\Doctrine\Service\EntityMock');
	}

	public function testGetEntityManager()
	{
		$this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->service->getEntityManager(), "->getEntityManager() instance Doctrine EntityManger");
		$this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->service->entityManager, "->entityManager instance Doctrine EntityManger");
	}

	public function testGetRepository()
	{
		$this->assertInstanceOf('Doctrine\ORM\EntityRepository', $this->service->getRepository(), "->getRepository() is instaceof Doctrine EntityRepository");
		$this->assertInstanceOf('Doctrine\ORM\EntityRepository', $this->service->repository, "->repository is instaceof Doctrine EntityRepository");
	}

	public function testGetClassMetadata()
	{
		$this->assertInstanceOf('Doctrine\ORM\Mapping\ClassMetadata', $this->service->getClassMetadata(), "->getClassMetadata() is instaceof Doctrine ClassMetadata");
		$this->assertInstanceOf('Doctrine\ORM\Mapping\ClassMetadata', $this->service->classMetadata, "->classMetadata is instaceof Doctrine ClassMetadata");
	}
}

namespace NellaTests\Doctrine\Service;

/**
 * @entity
 */
class EntityMock extends \Nella\Doctrine\Entity { }