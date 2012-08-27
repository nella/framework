<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

use Nette\Diagnostics\Debugger;

if (!defined('LIBS_DIR')) {
	define('LIBS_DIR', realpath(__DIR__ . '/../'));
}

// Load and init Nette Framework
if (!defined('NETTE')) {
	$file = LIBS_DIR . '/Nette/loader.php';
	if (!file_exists($file)) {
		die('You must load Nette Framework first');
	}
	require_once $file;
}

/**
 * Load and configure Nella Framework
 */
define('NELLA_FRAMEWORK', TRUE);
define('NELLA_FRAMEWORK_VERSION_ID', 20000); // v2.0.0
@header('X-Powered-By: Nette Framework with Nella Framework'); // @ - headers may be sent

require_once __DIR__ . '/SplClassLoader.php';
$map = array(
	'Nella' => __DIR__,
);
Nella\SplClassLoader::getInstance($map)->register();

require_once __DIR__ . '/shortcuts.php';

