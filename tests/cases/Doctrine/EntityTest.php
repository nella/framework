<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Doctrine;

class EntityTest extends \Nella\Testing\TestCase
{
	/** @var Entity\EntityMock */
	private $entity;

	public function setup()
	{
		$this->entity = new Entity\EntityMock;
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\Doctrine\Entity', $this->entity);
	}

	public function testGetId()
	{
		$this->assertNull($this->entity->getId(), "->getId() default is NULL");
		$this->assertNull($this->entity->id, "->id default is NULL");

		$this->entity = new Entity\EntityMock(123);
		$this->assertEquals(123, $this->entity->getId(), "->getId() is 123");
		$this->assertEquals(123, $this->entity->id, "->id is 123");
	}
}

namespace NellaTests\Doctrine\Entity;

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