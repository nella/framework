<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

use Nella\SplClassLoader,
	Nette\Diagnostics\Debugger;

// Load libs
$params = array(
	'appName' => "Nella Framework Test Suite", 
	'rootDir' => __DIR__ . "/..", 
	'appDir' => __DIR__, 
	'wwwDir' => __DIR__, 
	'libsDir' => __DIR__ . "/../vendors", 
	'tempDir' => __DIR__ . "/temp", 
	'storageDir' => "%tempDir",
	'imageCacheDir' => "%tempDir",
);

require_once $params['libsDir'] . "/Nette/loader.php";
require_once __DIR__ . "/../Nella/SplClassLoader.php";
SplClassLoader::getInstance(array(
	'Nella' => __DIR__ . "/../Nella",
	'NellaTests' => __DIR__,
	'Doctrine' => $params['libsDir'] . "/Doctrine",
	'Symfony' => $params['libsDir'] . "/Symfony"
))->register();

// Setup Nette profiler
Debugger::$strictMode = TRUE;
Debugger::enable(Debugger::DEVELOPMENT, FALSE);

// Init DI Container
$configurator = new \Nella\Configurator('Nette\DI\Container', $params);

$container = $configurator->loadConfig(__DIR__ . "/config.neon", "test");

$container->addService('model', function() {
	$context = new \Nella\Doctrine\Container;
	$context->addService('entityManager', \Doctrine\Tests\Mocks\EntityManagerMock::create(
		new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver)
	));	
	return $context;
});

// Set DI Container
Nette\Environment::setContext($container);
