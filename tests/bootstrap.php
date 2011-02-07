<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

// Load Nette Framework
require_once __DIR__ . "/../../Nette/loader.php";

Nette\Debug::enable(Nette\Debug::PRODUCTION);
Nette\Debug::$logDirectory = __DIR__;

Nette\Debug::$maxDepth = 10;
Nette\Debug::$maxLen = 4096;

// Init Nette Framework robot loader
$loader = new Nette\Loaders\RobotLoader;
$loader->setCacheStorage(new Nette\Caching\MemoryStorage);
$loader->addDirectory(__DIR__ . "/../../Doctrine");
$loader->addDirectory(__DIR__ . "/../Nella");
$loader->addDirectory(__DIR__);
$loader->register();

define('APP_DIR', __DIR__);
define('NELLA_FRAMEWORK_DIR', __DIR__ . "/../Nella");