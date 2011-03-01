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

class TimestampableListenerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Nella\Models\TimestampableListener */
	private $listener;
	
	public function setUp()
	{
		$this->listener = new \Nella\Models\TimestampableListener;
	}
	
	public function testGetSubscribedEvents()
	{
		$this->assertEquals(array(\Doctrine\ORM\Events::preUpdate), $this->listener->getSubscribedEvents(), "is Doctrine\\ORM\\Events::preUpdate");
		$this->assertEquals(array(\Doctrine\ORM\Events::preUpdate), $this->listener->subscribedEvents, "is Doctrine\\ORM\\Events::preUpdate");
	}
	
	public function testPreUpdate()
	{
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->listener, "instance of Doctrine\\Common\\EventSubscriber");
		
		$em = \Doctrine\Tests\Mocks\EntityManagerMock::create(new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver));
		$entity = new \NellaTests\Models\TimestampableEntityMock;
		$em->persist($entity);
		
		$changeSet = array('datetime' => new \DateTime("1970-1-1"));
		$args = new \Doctrine\ORM\Event\PreUpdateEventArgs($entity, $em, $changeSet);
		
		$this->assertNull($entity->getDatetime(), "is default value NULL");
		
		$this->listener->preUpdate($args);
		
		$this->assertInstanceOf('DateTime', $entity->getDateTime(), "is datetime updated");
		//$this->assertNotEquals(new \DateTime("1970-1-1"), $doc->getDatetime(), "does not 1970-1-1");
	}
}
