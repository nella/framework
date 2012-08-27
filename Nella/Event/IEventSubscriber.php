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
 * Event subscriber interface
 * 
 * @author Michael Moravec
 */
interface IEventSubscriber
{
	/**
	 * Gives information about all event the object is interested of
	 * The returning array should be in format 'eventName' => 'methodName'
	 * Each method will receive an argument with EventArgs object
	 * 
	 * @return array
	 */
	public function getSubscribedEvents();
}

