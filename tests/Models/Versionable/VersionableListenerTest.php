<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Models;

require_once __DIR__ . "/../../bootstrap.php";

class VersionableListenerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Models\VersionableListener */
	private $listener;
	
	public function setUp()
	{
		$this->listener = new \Nella\Models\VersionListener;
	}
	
	public function testGetSubscribedEvents()
	{
		$this->assertEquals(array(\Doctrine\ORM\Events::postUpdate), $this->listener->getSubscribedEvents(), "is Doctrine\\ORM\\Events::onFlush");
		$this->assertEquals(array(\Doctrine\ORM\Events::postUpdate), $this->listener->subscribedEvents, "is Doctrine\\ORM\\Events::onFlush");
	}
	
	public function testPostUpdaetInsert()
	{
		$this->markTestSkipped();
		return;
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->listener, "instance of Doctrine\\Common\\EventSubscriber");
		$em = \Doctrine\Tests\Mocks\EntityManagerMock::create(new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver));
		$args = new \Doctrine\ORM\Event\OnFlushEventArgs($em);
		$entity = new VersionableEntityMock;
		$entity->setData("foo");
		$em->persist($entity);
		
		$this->listener->postUpdate($args);
		
		$uow = $em->getUnitOfWork();
		$ve = NULL;
		foreach ($uow->getScheduledEntityInsertions() as $ventity) {
			if ($ventity instanceof \Nella\Models\VersionEntity) {
				$ve = $ventity;
			}
		}
		
		$this->assertNotNull($ve, "is existing snapshot");
		$this->assertEquals(get_class($entity), $ve->getEntityClass(), "validate snapshot class");
		$this->assertEquals($entity->takeSnapshot(), $ve->getEntityData(), "validate snapshot data");
	}
}
