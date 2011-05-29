<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Models\Listeners;

class UserableTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Security\IdentiyEntity */
	/** @var \Nella\Models\Listeners\Userable */
	private $listener;

	public function setUp()
	{
		$identity = new \Nella\Security\IdentityEntity;
		$identity->setUsername("test");
		$this->listener = new \Nella\Models\Listeners\Userable($identity);
	}

	public function testGetSubscribedEvents()
	{
		$this->assertEquals(array(
			\Doctrine\ORM\Events::preUpdate,
			\Doctrine\ORM\Events::loadClassMetadata
		), $this->listener->getSubscribedEvents(), "is Doctrine\\ORM\\Events::preUpdate");
		$this->assertEquals(array(
			\Doctrine\ORM\Events::preUpdate,
			\Doctrine\ORM\Events::loadClassMetadata
		), $this->listener->subscribedEvents, "is Doctrine\\ORM\\Events::preUpdate");
	}

	public function testCreator()
	{
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->listener, "instance of Doctrine\\Common\\EventSubscriber");

		$em = \Doctrine\Tests\Mocks\EntityManagerMock::create(new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver));

		$args = new \Doctrine\ORM\Event\LoadClassMetadataEventArgs($em->getClassMetadata('NellaTests\Models\Listeners\UserableEntityMock'), $em);
		$this->listener->loadClassMetadata($args);
		$entity = new UserableEntityMock;

		$em->persist($entity);

		$changeSet = array();
		$args = new \Doctrine\ORM\Event\PreUpdateEventArgs($entity, $em, $changeSet);

		$this->assertNull($entity->getCreator(), "is default creator value NULL");
		$this->assertNull($entity->getEditor(), "is default creator value NULL");

		$this->listener->preUpdate($args);

		$this->assertInstanceOf('Nella\Security\IdentityEntity', $entity->getCreator(), "is creator updated");
		$this->assertInstanceOf('Nella\Security\IdentityEntity', $entity->getEditor(), "is editor updated");

		$this->assertEquals("test", $entity->getCreator()->username, "is creator username 'test'");
		$this->assertEquals("test", $entity->getEditor()->username, "is editor username 'test'");

		$entity->clean();

		$this->listener->preUpdate($args);

		$this->assertInstanceOf('Nella\Security\IdentityEntity', $entity->getCreator(), "is creator updated");
		$this->assertInstanceOf('Nella\Security\IdentityEntity', $entity->getEditor(), "is editor updated");

		$this->assertNull($entity->getCreator()->username, "is creator username NULL");
		$this->assertEquals("test", $entity->getEditor()->username, "is editor username 'test'");
	}
}
