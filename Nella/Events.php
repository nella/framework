<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella;

/**
 * Events
 *
 * @author Michael Moravec
 */
final class Events
{
	// Application events
	const APPLICATION_STARTUP = 'Nella\Application::startup';
	const APPLICATION_REQUEST = 'Nella\Application::request';
	const APPLICATION_RESPONSE = 'Nella\Application::response';
	const APPLICATION_SHUTDOWN = 'Nella\Application::shutdown';
	const APPLICATION_ERROR = 'Nella\Application::error';

	/**
	 * @throws \Nette\StaticClassException
	 */
	public function __construct()
	{
		throw new \Nette\StaticClassException;
	}
}

