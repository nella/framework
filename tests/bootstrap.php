<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

// Define dirs constants
define('APP_DIR', __DIR__);
if (file_exists(__DIR__ . "/../src/Nella")) {
	define('NELLA_FRAMEWORK_DIR', APP_DIR . "/../src/Nella");
} else {
	define('NELLA_FRAMEWORK_DIR', APP_DIR . "/../Nella");
}
if (file_exists(APP_DIR . "/../dependency")) {
	define('DEPENDENCY_DIR', APP_DIR . "/../dependency");
} else {
	define('DEPENDENCY_DIR', APP_DIR . "/../../dependency");
}

// Load Nette Framework
require_once DEPENDENCY_DIR . "/Nette/loader.php";

// Setup Nette profiler
Nette\Debug::enable();
Nette\Debug::$logDirectory = APP_DIR;
Nette\Debug::$maxLen = 4096;

/**
 * @param mixed
 * @param string
 */
function fdump($var, $file = NULL)
{
	file_put_contents($file ?: __DIR__ . "/dump.html", "<code><pre>" . \Nette\Debug::dump($var, true) . "</pre></code>", FILE_APPEND);
}

// Init Nette Framework robot loader
$loader = new Nette\Loaders\RobotLoader;
$loader->setCacheStorage(new Nette\Caching\MemoryStorage);
$loader->addDirectory(DEPENDENCY_DIR);
$loader->addDirectory(NELLA_FRAMEWORK_DIR);
$loader->addDirectory(APP_DIR);
$loader->register();

