<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Models;

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
		$this->assertEquals(array(\Doctrine\ODM\MongoDB\Events::preUpdate), $this->listener->getSubscribedEvents(), "is Doctrine\\ODM\\MongoDB\\Events::preUpdate");
		$this->assertEquals(array(\Doctrine\ODM\MongoDB\Events::preUpdate), $this->listener->subscribedEvents, "is Doctrine\\ODM\\MongoDB\\Events::preUpdate");
	}
	
	public function testPreUpdate()
	{
		$this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->listener, "instance of Doctrine\\Common\\EventSubscriber");
		
		$dm = \Doctrine\ODM\MongoDB\Tests\Mocks\DocumentManagerMock::create();
		$doc = new \NellaTests\Models\TimestampableDocumentMock;
		$dm->persist($doc);
		
		$changeSet = array('datetime' => new \DateTime("1970-1-1"));
		$args = new \Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs($doc, $dm, $changeSet);
		
		$this->assertNull($doc->getDatetime(), "is default value NULL");
		
		$this->listener->preUpdate($args);
		
		$this->assertInstanceOf('DateTime', $doc->getDateTime(), "is datetime updated");
		//$this->assertNotEquals(new \DateTime("1970-1-1"), $doc->getDatetime(), "does not 1970-1-1");
	}
}