<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Event;

class EventDispatcherTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Event\EventDispatcher */
	private $eventManager;

	public function setup()
	{
		parent::setup();
		$this->eventManager = new \Nella\Event\EventDispatcher;
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Event\IEventDispatcher', $this->eventManager);
	}
	
	public function testDispatchEmptyEvent()
	{
		$this->assertNull($this->eventManager->dispatchEvent("foo"));
	}
	
	public function testAddEventListener()
	{
		$foo = 0;
		$this->eventManager->addEventListener('foo', function() use(&$foo) {
			$foo++;
		});
		
		$this->eventManager->dispatchEvent('foo');
		
		$this->assertEquals(1, $foo);
	}
	
	public function testGetListeners()
	{
		$this->assertEquals(array(), $this->eventManager->getListeners('foo'), "->getListeners('foo')");
		
		$callback = function() { return; };
		$this->eventManager->addEventListener('bar', $callback);
		
		$this->assertEquals(array($callback), $this->eventManager->getListeners('bar'), "->getListeners('bar')");
	}
	
	public function testGetAllListeners()
	{
		$this->assertEquals(array(), $this->eventManager->getAllListeners(), "->getAllListeners() - empty");
		$this->assertEquals(array(), $this->eventManager->allListeners, "->allListeners - empty");
		
		$callback = function() { return; };
		$this->eventManager->addEventListener('bar', $callback);
		
		$listeners = array('bar' => array($callback));
		$this->assertEquals($listeners, $this->eventManager->getAllListeners(), "->getAllListeners()");
		$this->assertEquals($listeners, $this->eventManager->allListeners, "->allListeners");
	}
	
	public function testHasListeners()
	{
		$this->assertFalse($this->eventManager->hasListeners('foo'), "->hasListeners('foo')");
		
		$this->eventManager->addEventListener('bar', function() { return; });
		
		$this->assertTrue($this->eventManager->hasListeners('bar'), "->hasListeners('bar')");
	}
	
	public function testRemoveEventListener()
	{
		$callback = function() { return; };
		$this->assertFalse($this->eventManager->hasListeners('foo'), "test init empty");
		$this->eventManager->removeEventListener('foo', $callback);
		$this->assertFalse($this->eventManager->hasListeners('foo'), "test init remove empty");
		
		$this->eventManager->addEventListener('foo', $callback);
		$this->assertTrue($this->eventManager->hasListeners('foo'), "test add one");
		
		$this->eventManager->removeEventListener('foo', $callback);
		$this->assertFalse($this->eventManager->hasListeners('foo'), "test remove one");
	}
	
	public function testDispatchEvent()
	{
		$this->eventManager->addEventListener('foo', function(\Nella\Event\Args\Container $args) {
			$args->container->addService('bar', function() { return; });
		});
		
		$container = new \Nette\DI\Container;
		$this->eventManager->dispatchEvent('foo', new \Nella\Event\Args\Container($container));
		$this->assertTrue($container->hasService('bar'));
	}
	
	public function testEventSubscriber()
	{
		$subscriber = new EventSubscriber;
		$this->eventManager->addEventSubscriber($subscriber);
		
		$container = new \Nette\DI\Container;
		$this->eventManager->dispatchEvent('foo', new \Nella\Event\Args\Container($container));
		$this->assertTrue($container->hasService('bar'));
	}
	
	public function testRemoveSubscriber()
	{
		$subscriber = new EventSubscriber;
		$this->assertFalse($this->eventManager->hasListeners('foo'), "test init empty");
		$this->eventManager->removeEventSubscriber($subscriber);
		$this->assertFalse($this->eventManager->hasListeners('foo'), "test init remove empty");
		
		$this->eventManager->addEventSubscriber($subscriber);
		$this->assertTrue($this->eventManager->hasListeners('foo'), "test add one");
		
		$this->eventManager->removeEventSubscriber($subscriber);
		$this->assertFalse($this->eventManager->hasListeners('foo'), "test remove one");
		
	}
}

class EventSubscriber extends \Nette\Object implements \Nella\Event\IEventSubscriber
{
	public function getSubscribedEvents()
	{
		return array('foo' => "listener");
	}

	public function listener(\Nella\Event\Args\Container $args)
	{
		$args->container->addService('bar', function() { return; });
	}
}