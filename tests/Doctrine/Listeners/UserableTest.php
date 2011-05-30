<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Doctrine\Listeners;

class UserableTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Doctrine\Listeners\Userable */
	private $listener;

	public function setup()
	{
		$identity = new \Nella\Security\IdentityEntity;
		$identity->setLang("test");
		$this->listener = new \Nella\Doctrine\Listeners\Userable($identity);
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

		$args = new \Doctrine\ORM\Event\LoadClassMetadataEventArgs($em->getClassMetadata('NellaTests\Doctrine\Listeners\Userable\EntityMock'), $em);
		$this->listener->loadClassMetadata($args);
		$entity = new Userable\EntityMock;

		$em->persist($entity);

		$changeSet = array();
		$args = new \Doctrine\ORM\Event\PreUpdateEventArgs($entity, $em, $changeSet);

		$this->assertNull($entity->getCreator(), "is default creator value NULL");
		$this->assertNull($entity->getEditor(), "is default creator value NULL");

		$this->listener->preUpdate($args);

		$this->assertInstanceOf('Nella\Security\IdentityEntity', $entity->getCreator(), "is creator updated");
		$this->assertInstanceOf('Nella\Security\IdentityEntity', $entity->getEditor(), "is editor updated");

		$this->assertEquals("test", $entity->getCreator()->lang, "is creator username 'test'");
		$this->assertEquals("test", $entity->getEditor()->lang, "is editor username 'test'");

		$entity->clean();

		$this->listener->preUpdate($args);

		$this->assertInstanceOf('Nella\Security\IdentityEntity', $entity->getCreator(), "is creator updated");
		$this->assertInstanceOf('Nella\Security\IdentityEntity', $entity->getEditor(), "is editor updated");

		$this->assertNull($entity->getCreator()->lang, "is creator username NULL");
		$this->assertEquals("test", $entity->getEditor()->lang, "is editor username 'test'");
	}
}

namespace NellaTests\Doctrine\Listeners\Userable;

/**
 * @entity
 */
class EntityMock extends \Nella\Doctrine\Entity
{
	/**
	 * @creator
	 * @manyToOne(targetEntity="Nella\Security\IdentityEntity")
	 * @joinColumn(name="user_id", referencedColumnName="id")
	 * @var \Nella\Security\IdentityEntity
	 */
	private $creator;
	/**
	 * @editor
	 * @manyToOne(targetEntity="Nella\Security\IdentityEntity")
	 * @joinColumn(name="user_id", referencedColumnName="id")
	 * @var \Nella\Security\IdentityEntity
	 */
	private $editor = NULL;

	/**
	 * @return \Nella\Security\IdentityEntity
	 */
	public function getCreator()
	{
		return $this->creator;
	}

	/**
	 * @return \Nella\Security\IdentityEntity
	 */
	public function getEditor()
	{
		return $this->editor;
	}

	public function clean()
	{
		$this->editor = $this->creator = new \Nella\Security\IdentityEntity;
	}
}
