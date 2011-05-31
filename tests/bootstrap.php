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
define('VENDORS_DIR', __DIR__ . "/../vendors");
define('NELLA_FRAMEWORK_DIR', __DIR__ . "/../Nella");
require_once VENDORS_DIR . "/Nette/loader.php";
require_once NELLA_FRAMEWORK_DIR . "/SplClassLoader.php";
SplClassLoader::getInstance(array(
	'Nella' => __DIR__ . "/../Nella",
	'NellaTests' => __DIR__,
	'Doctrine' => VENDORS_DIR . "/Doctrine",
	'Symfony' => VENDORS_DIR . "/Symfony"
))->register();

// Setup Nette profiler
Debugger::$strictMode = TRUE;
Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . "/log");

// Init DI Container
$container = new \Nette\DI\Container;
$container->params = Nette\ArrayHash::from(array(
	'appDir' => __DIR__,
	'libsDir' => VENDORS_DIR,
	'tempDir' => __DIR__ . "/temp",
	'uploadDir' => __DIR__ . "/uploads",
	'storageDir' => "%tempDir",
	'imageCacheDir' => "%tempDir",
	'productionMode' => FALSE,
	'consoleMode' => TRUE,
	'flashes' => array(
		'success' => "success",
		'error' => "error",
		'info' => "info",
		'warning' => "warning",
	),
	'database' => array(
		'driver' => "pdo_mysql",
		'memory' => TRUE,
	),
));
$container->addService('cacheStorage', 'Nette\Caching\Storages\DevNullStorage');
$container->addService('templateCacheStorage', function(Nette\DI\Container $container) {
	return $container->cacheStorage;
});
$container->addService('user', 'NellaTests\Mocks\User');
$container->addService('httpRequest', function() {
	$factory = new Nette\Http\RequestFactory;
	$factory->setEncoding('UTF-8');
	return $factory->createHttpRequest();
});
$container->addService('httpResponse', 'Nette\Http\Response');
$container->addService('components', 'Nella\Application\UI\ComponentContainer');
$container->addService('macros', 'Nella\Latte\Macros');
$container->addService('model', function() {
	$context = new \Nella\Doctrine\Container;
	$context->addService('entityManager', \Doctrine\Tests\Mocks\EntityManagerMock::create(
		new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver)
	));
	return $context;
});
$container->addService('latteEngine', 'Nella\Latte\Engine');

// Set DI Container
Nette\Environment::setContext($container);
