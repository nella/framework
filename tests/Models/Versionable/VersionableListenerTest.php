<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Models;

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
		$this->assertEquals(array(\Doctrine\ODM\MongoDB\Events::onFlush), $this->listener->getSubscribedEvents(), "is Doctrine\\ODM\\MongoDB\\Events::onFlush");
		$this->assertEquals(array(\Doctrine\ODM\MongoDB\Events::onFlush), $this->listener->subscribedEvents, "is Doctrine\\ODM\\MongoDB\\Events::onFlush");
	}
	
	public function testOnFlushInsert()
	{
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->listener, "instance of Doctrine\\Common\\EventSubscriber");
		$dm = \Doctrine\ODM\MongoDB\Tests\Mocks\DocumentManagerMock::create();
		$args = new \Doctrine\ODM\MongoDB\Event\OnFlushEventArgs($dm);
		$doc = new VersionableDocumentMock;
		$doc->setData("foo");
		$dm->persist($doc);
		
		$this->listener->onFlush($args);
		
		$uow = $dm->getUnitOfWork();
		$vd = NULL;
		foreach ($uow->getScheduledDocumentInsertions() as $document) {
			if ($document instanceof \Nella\Models\VersionDocument) {
				$vd = $document;
			}
		}
		
		$this->assertNotNull($vd, "is existing snapshot");
		$this->assertEquals(get_class($doc), $vd->getDocumentClass(), "validate snapshot class");
		$this->assertEquals($doc->takeSnapshot(), $vd->getDocumentData(), "validate snapshot data");
	}
}