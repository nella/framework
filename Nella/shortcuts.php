<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

if (!class_exists('Nette\Framework')) {
	die('You must load Nette Framework first');
}

/**
 * Load and configure Nella Framework
 */
define('NELLA_FRAMEWORK', TRUE);
define('NELLA_FRAMEWORK_VERSION_ID', 20000); // v2.0.0
@header('X-Powered-By: Nette Framework with Nella Framework'); // @ - headers may be sent

/**
 * @param mixed
 * @param string
 * @return void
 */
function barDump($var, $title = NULL)
{
	Nette\Diagnostics\Debugger::barDump($var, $title);
}

