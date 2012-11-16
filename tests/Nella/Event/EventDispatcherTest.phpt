<?php
/**
 * Test: Nella\Event\EventDispatcher
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Event;

use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';
require_once MOCKS_DIR . '/Config/Configurator.php';

class EventDispatcherTest extends \Tester\TestCase
{
	/** @var \Nella\Event\EventDispatcher */
	private $eventManager;

	public function setUp()
	{
		parent::setUp();
		$this->eventManager = new \Nella\Event\EventDispatcher;
	}

	public function testInstance()
	{
		Assert::true($this->eventManager instanceof \Nella\Event\IEventDispatcher);
	}

	public function testDispatchEmptyEvent()
	{
		Assert::null($this->eventManager->dispatchEvent("foo"));
	}

	public function testAddEventListener()
	{
		$foo = 0;
		$this->eventManager->addEventListener('foo', function() use(&$foo) {
			$foo++;
		});

		$this->eventManager->dispatchEvent('foo');

		Assert::equal(1, $foo);
	}

	public function testGetListeners()
	{
		Assert::equal(array(), $this->eventManager->getListeners('foo'), "->getListeners('foo')");

		$callback = function() { return; };
		$this->eventManager->addEventListener('bar', $callback);

		$liteners = $this->eventManager->getListeners('bar');

		Assert::equal(1, count($liteners));
		Assert::same($callback, $liteners[0]);
	}

	public function testGetAllListeners()
	{
		Assert::equal(array(), $this->eventManager->getAllListeners(), "->getAllListeners() - empty");
		Assert::equal(array(), $this->eventManager->allListeners, "->allListeners - empty");

		$callback = function() { return; };
		$this->eventManager->addEventListener('bar', $callback);

		$liteners = $this->eventManager->getAllListeners();

		Assert::true(array_key_exists('bar', $liteners));
		Assert::equal(1, count($liteners['bar']));
		Assert::same($callback, $liteners['bar'][0]);
	}

	public function testHasListeners()
	{
		Assert::false($this->eventManager->hasListeners('foo'), "->hasListeners('foo')");

		$this->eventManager->addEventListener('bar', function() { return; });

		Assert::true($this->eventManager->hasListeners('bar'), "->hasListeners('bar')");
	}

	public function testRemoveEventListener()
	{
		$callback = function() { return; };
		Assert::false($this->eventManager->hasListeners('foo'), "test init empty");
		$this->eventManager->removeEventListener('foo', $callback);
		Assert::false($this->eventManager->hasListeners('foo'), "test init remove empty");

		$this->eventManager->addEventListener('foo', $callback);
		Assert::true($this->eventManager->hasListeners('foo'), "test add one");

		$this->eventManager->removeEventListener('foo', $callback);
		Assert::false($this->eventManager->hasListeners('foo'), "test remove one");
	}

	public function testDispatchEvent()
	{
		$this->eventManager->addEventListener('foo', function(\Nella\Event\Args\Container $args) {
			$args->container->addService('bar', function() { return; });
		});

		$container = new \Nette\DI\Container;
		$this->eventManager->dispatchEvent('foo', new \Nella\Event\Args\Container($container));
		Assert::true($container->hasService('bar'));
	}

	public function testEventSubscriber()
	{
		$subscriber = new EventSubscriber;
		$this->eventManager->addEventSubscriber($subscriber);

		$container = new \Nette\DI\Container;
		$this->eventManager->dispatchEvent('foo', new \Nella\Event\Args\Container($container));
		Assert::true($container->hasService('bar'));
	}

	public function testRemoveSubscriber()
	{
		$subscriber = new EventSubscriber;
		Assert::false($this->eventManager->hasListeners('foo'), "test init empty");
		$this->eventManager->removeEventSubscriber($subscriber);
		Assert::false($this->eventManager->hasListeners('foo'), "test init remove empty");

		$this->eventManager->addEventSubscriber($subscriber);
		Assert::true($this->eventManager->hasListeners('foo'), "test add one");

		$this->eventManager->removeEventSubscriber($subscriber);
		Assert::false($this->eventManager->hasListeners('foo'), "test remove one");

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

id(new EventDispatcherTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
