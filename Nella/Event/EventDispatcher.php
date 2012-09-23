<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, 
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Event;

/**
 * Event Dispatcher
 * Holds all informations about currently registered
 * event listeners or subscribers
 *
 * There are two types of registration:
 *  1. via IEventSubscriber
 *  2. via raw callback (aka callable)
 *
 * This solution is similar to Doctrine\Common\EventManager.
 *
 * @author Michael Moravec
 * 
 * @property-read array $allListeners
 */
final class EventDispatcher extends \Nette\Object implements IEventDispatcher
{
	/** @var array */
	private $listeners = array();

	/**
	 * @param string
	 * @param EventArgs
	 */
	public function dispatchEvent($event, EventArgs $args = NULL)
	{
		if (empty($this->listeners[$event])) {
			return; //nothing to do here
		}

		if ($args === NULL) {
			$args = new Args\Void;
		}

		foreach ($this->listeners[$event] as $listener) {
			call_user_func($listener, $args);
		}
	}

	/**
	 * @param string
	 * @param callable
	 */
	public function addEventListener($event, $listener)
	{
		if (!isset($this->listeners[$event])) {
			$this->listeners[$event] = array();
		}

		if (!is_callable($listener)) {
			throw new \InvalidArgumentException('Listener must be callable.');
		}

		$this->listeners[$event][] = $listener;
	}

	/**
	 * @param IEventSubscriber
	 */
	public function addEventSubscriber(IEventSubscriber $subscriber)
	{
		foreach ($subscriber->getSubscribedEvents() as $event => $listener) {
			$this->addEventListener($event, array($subscriber, $listener));
		}
	}

	/**
	 * @param string
	 * @return array
	 */
	public function getListeners($event)
	{
		return isset($this->listeners[$event]) ? $this->listeners[$event] : array();
	}

	/**
	 * @return array
	 */
	public function getAllListeners()
	{
		return $this->listeners;
	}

	/**
	 * @param string
	 * @return bool
	 */
	public function hasListeners($event)
	{
		return !empty($this->listeners[$event]);
	}

	/**
	 * @param string
	 * @param callable
	 */
	public function removeEventListener($event, $listener)
	{
		if (empty($this->listeners[$event])) {
			return;
		}

		if (($key = array_search($listener, $this->listeners[$event])) !== FALSE) {
			unset($this->listeners[$event][$key]);
		}
	}

	/**
	 * @param IEventSubscriber
	 */
	public function removeEventSubscriber(IEventSubscriber $subscriber)
	{
		foreach ($subscriber->getSubscribedEvents() as $event => $listener) {
			$this->removeEventListener($event, array($subscriber, $listener));
		}
	}
}

