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

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Debugger::$strictMode = TRUE;
Debugger::enable(Debugger::DEVELOPMENT, TEMP_DIR . '/log');

function id($val) {
	return $val;
}

if (extension_loaded('xdebug')) {
	Tester\CodeCoverage\Collector::start(__DIR__ . '/coverage.dat');
}
