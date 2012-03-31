<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

use Nette\Diagnostics\Debugger;

// Load libs
$params = array(
	'appDir' => __DIR__,
	'wwwDir' => __DIR__,
	'libsDir' => __DIR__ . "/../vendors",
	'tempDir' => __DIR__ . "/temp",
	'fixturesDir' => __DIR__ . "/fixtures"
);

require_once $params['libsDir'] . "/pear-nette/Nette/Nette/loader.php";
require_once $params['libsDir'] . "/.composer/autoload.php";
require_once __DIR__ . "/../Nella/loader.php";
Nella\SplClassLoader::getInstance()
    ->addNamespaceAlias('NellaTests', __DIR__ . '/cases')
    ->addNamespaceAlias('Doctrine', $params['libsDir'] . "/Doctrine")
	->addNamespaceAlias('Symfony', $params['libsDir'] . "/Symfony");

// Setup Nette profiler
//Debugger::$browser = '';
Debugger::$strictMode = TRUE;
Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . "/temp/log");

// Init DI Container
$configurator = new \Nette\Config\Configurator;
$configurator->addParameters($params);
$configurator->setTempDirectory($params['tempDir']);

$configurator->addConfig(__DIR__ . "/config.neon", FALSE);
$container = $configurator->createContainer();

require_once __DIR__ . "/mocks/EntityManagerMock.php";
require_once __DIR__ . "/mocks/UserStorage.php";

\Nette\Environment::setContext($container);
