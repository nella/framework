<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

use Nette\Diagnostics\Debugger;

define('TESTS_DIR', __DIR__);
define('VENDOR_DIR', TESTS_DIR . '/../vendor');
define('FIXTURES_DIR', TESTS_DIR . '/fixtures');
define('MOCKS_DIR', TESTS_DIR . '/mocks');
define('TEMP_DIR', TESTS_DIR . '/temp');

require_once VENDOR_DIR . '/autoload.php';
require_once VENDOR_DIR . '/nette/tester/Tester/bootstrap.php';

// configure environment
date_default_timezone_set('Europe/Prague');
//TestHelpers::purge(TEMP_DIR);

Debugger::$strictMode = TRUE;
Debugger::enable(Debugger::DEVELOPMENT, TEMP_DIR . '/log');

if (extension_loaded('xdebug')) {
	xdebug_disable();
	TestHelpers::startCodeCoverage(TESTS_DIR . '/coverage.dat');
}
