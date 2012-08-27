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
 * Event Dispatcher interface
 * 
 * @author Michael Moravec
 */
interface IEventDispatcher
{
	/**
	 * @param string event name
	 * @param EventArgs|NULL event arguments
	 */
	public function dispatchEvent($event, EventArgs $args = NULL);

	/**
	 * @param string event name
	 * @param callable event listener
	 * @throws \Nette\InvalidArgumentException
	 */
	public function addEventListener($event, /*callable *5.4*/ $listener);

	/**
	 * @param string event name
	 * @return array
	 */
	public function getListeners($event);

	/**
	 * @param string event name
	 * @return bool
	 */
	public function hasListeners($event);

	/**
	 * @return array
	 */
	public function getAllListeners();

	/**
	 * @param string event name
	 * @param callable event listener
	 */
	public function removeEventListener($event, /*callable *5.4*/ $listener);

	/**
	 * @param IEventSubscriber event subscriber
	 * @throws \Nette\InvalidArgumentException
	 */
	public function addEventSubscriber(IEventSubscriber $subscriber);

	/**
	 * @param IEventSubscriber event subscriber
	 */
	public function removeEventSubscriber(IEventSubscriber $subscriber);
}

