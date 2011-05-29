<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Models;

require_once __DIR__ . "/../bootstrap.php";

class EntityTest extends \PHPUnit_Framework_TestCase
{
	/** @var EntityMock */
	private $entity;

	public function setUp()
	{
		$this->entity = new EntityMock;
	}

	public function testGetId()
	{
		$this->assertNull($this->entity->getId(), "->getId() default is NULL");
		$this->assertNull($this->entity->id, "->id default is NULL");

		$this->entity = new EntityMock(123);
		$this->assertEquals(123, $this->entity->getId(), "->getId() is 123");
		$this->assertEquals(123, $this->entity->id, "->id is 123");
	}
}
