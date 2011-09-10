<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Doctrine\Listeners;

class VersionableTest extends \Nella\Testing\TestCase
{
	/** @var Nella\Doctrine\Listeners\Versionable */
	private $listener;

	public function setup()
	{
		$this->listener = new \Nella\Doctrine\Listeners\Version;
	}

	public function testGetSubscribedEvents()
	{
		$this->assertEquals(array(
			\Doctrine\ORM\Events::postPersist,
			\Doctrine\ORM\Events::onFlush
		), $this->listener->getSubscribedEvents(), "is Doctrine\\ORM\\Events::onFlush");
		$this->assertEquals(array(
			\Doctrine\ORM\Events::postPersist,
			\Doctrine\ORM\Events::onFlush
		), $this->listener->subscribedEvents, "is Doctrine\\ORM\\Events::onFlush");
	}

	public function testPostUpdaetInsert()
	{
		$this->markTestSkipped();
		return;
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->listener, "instance of Doctrine\\Common\\EventSubscriber");
		$em = \Doctrine\Tests\Mocks\EntityManagerMock::create(new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver));
		$args = new \Doctrine\ORM\Event\OnFlushEventArgs($em);
		$entity = new Versionable\EntityMock;
		$entity->setData("foo");
		$em->persist($entity);

		$this->listener->postUpdate($args);

		$uow = $em->getUnitOfWork();
		$ve = NULL;
		foreach ($uow->getScheduledEntityInsertions() as $ventity) {
			if ($ventity instanceof \Nella\Doctrine\Listeners\VersionEntity) {
				$ve = $ventity;
			}
		}

		$this->assertNotNull($ve, "is existing snapshot");
		$this->assertEquals(get_class($entity), $ve->getEntityClass(), "validate snapshot class");
		$this->assertEquals($entity->takeSnapshot(), $ve->getEntityData(), "validate snapshot data");
	}
}

namespace NellaTests\Doctrine\Listeners\Versionable;

/**
 * @entity
 */
class EntityMock extends \Nella\Doctrine\Entity implements \Nella\Doctrine\IVersionableEntity
{
	/** @column(type="string") */
	private $data;

	/**
	 * @return string
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param string
	 * @return EntityMock
	 */
	public function setData($data)
	{
		$data = trim($data);
		$this->data = $data == "" ? NULL : $data;
		return $this;
	}

	public function takeSnapshot()
	{
		return serialize(array('data' => $this->data));
	}
}
