<?php
/**
 * Test: Nella\Doctrine\Entity
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Doctrine\EntityTest
 */

namespace Nella\Tests\Doctrine;

use Assert;

require_once __DIR__ . '/../../bootstrap.php';

class EntityTest extends \TestCase
{
	/** @var Entity\EntityMock */
	private $entity;

	public function setup()
	{
		$this->entity = new Entity\EntityMock;
	}

	public function testInstanceOf()
	{
		Assert::true($this->entity instanceof \Nella\Doctrine\Entity);
	}

	public function testGetId()
	{
		Assert::null($this->entity->getId(), "->getId() default is NULL");
		Assert::null($this->entity->id, "->id default is NULL");

		$this->entity = new Entity\EntityMock(123);
		Assert::equal(123, $this->entity->getId(), "->getId() is 123");
		Assert::equal(123, $this->entity->id, "->id is 123");
	}
}

namespace Nella\Tests\Doctrine\Entity;

/**
 * @entity
 */
class EntityMock extends \Nella\Doctrine\Entity
{
	/**
	 * @param int
	 */
	public function __construct($id = NULL)
	{
		$ref = new \Nette\Reflection\Property('Nella\Doctrine\Entity', 'id');
		$ref->setAccessible(TRUE);
		$ref->setValue($this, $id);
		$ref->setAccessible(FALSE);
	}
}
