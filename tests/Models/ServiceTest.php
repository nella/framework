<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Models;

class ServiceTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Models\Service */
	private $service;
	/** @var \Nella\Models\Container */
	private $container;

	protected function setup()
	{
		$this->container = new \Nella\Models\Container;
		$this->service = new Service\ServiceMock($this->container, 'NellaTests\Models\Service\EntityMock');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testInstanceNonExistEntityClass()
	{
		$this->service = new Service\ServiceMock($this->container, 'NonExistsClass');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testInstanceInvalidEntityClass()
	{
		$this->service = new Service\ServiceMock($this->container, get_called_class());
	}

	public function testGetContainer()
	{
		$this->assertSame($this->container, $this->service->getContainer(), "->getContainer()");
		$this->assertSame($this->container, $this->service->container, "->container");
	}

	public function testGetEntityClass()
	{
		$this->assertEquals('NellaTests\Models\Service\EntityMock', $this->service->getEntityClass(), "->getEntityClass()");
		$this->assertEquals('NellaTests\Models\Service\EntityMock', $this->service->entityClass, "->entityClass");
	}

	public function testCreate()
	{
		$entity = $this->service->create(array('data' => "foo"));
		$this->assertInstanceOf('NellaTests\Models\Service\EntityMock', $entity, "test instance");
		$this->assertEquals("foo", $entity->getData(), "test data");
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testCreateInvalidValues()
	{
		$this->service->create(NULL);
	}

	public function testUpdate()
	{
		$entity = new Service\EntityMock;
		$entity->setData("bar");
		$entity = $this->service->update($entity, array('data' => "foo"));
		$this->assertEquals("foo", $entity->getData());
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testUpdateInvalidValues()
	{
		$entity = new Service\EntityMock;
		$entity->setData("bar");
		$this->service->update($entity, NULL);
	}
}

namespace NellaTests\Models\Service;

class ServiceMock extends \Nella\Models\Service implements \Nella\Models\IService
{
	public function delete(\Nella\Models\IEntity $entity)
	{
		throw new \Nette\NotImplementedException;
	}
}

class EntityMock implements \Nella\Models\IEntity
{
	private $data;

	public function getId()
	{
		return 1;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
}