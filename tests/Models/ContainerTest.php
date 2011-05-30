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

class ContainerTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Models\Container */
	private $container;

	protected function setup()
	{
		$this->container = new \Nella\Models\Container;
		$this->container->setDefaultServiceClass('NellaTests\Models\Container\ServiceMock');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testSetDefaultServiceClassNonExistClass()
	{
		$this->container->setDefaultServiceClass('NonExistClass');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testSetDefaultServiceClassInvalidClass()
	{
		$this->container->setDefaultServiceClass(get_called_class());
	}

	public function testGetService()
	{
		$service = $this->container->getService('NellaTests\Models\Container\EntityMock');
		$this->assertInstanceOf('NellaTests\Models\Container\ServiceMock', $service, "test instance");
		$this->assertEquals('NellaTests\Models\Container\EntityMock', $service->getEntityClass());
	}

	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testGetServiceNotSetDefaultServiceClass()
	{
		$container = new \Nella\Models\Container;
		$container->getService('NellaTests\Models\Container\EntityMock');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testGetServiceNonExistEntityClass()
	{
		$this->container->getService('NonExistClass');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testGetServiceInvalidEntityClass()
	{
		$this->container->getService(get_called_class());
	}

	public function testSetService()
	{
		$service = new Container\Service2Mock($this->container, 'NellaTests\Models\Container\EntityMock');
		$this->container->setService($service);
		$service = $this->container->getService('NellaTests\Models\Container\EntityMock');
		$this->assertInstanceOf('NellaTests\Models\Container\Service2Mock', $service, "test instance");
		$this->assertEquals('NellaTests\Models\Container\EntityMock', $service->getEntityClass());
	}
}

namespace NellaTests\Models\Container;

class ServiceMock extends \Nella\Models\Service implements \Nella\Models\IService
{
	public function delete(\Nella\Models\IEntity $entity)
	{
		throw new \Nette\NotImplementedException;
	}
}

class Service2Mock extends ServiceMock { }

class EntityMock implements \Nella\Models\IEntity
{
	public function getId()
	{
		return 1;
	}
}