<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

use Nette\Diagnostics\Debugger;

// Load and init Nette Framework
if (!defined('NETTE')) {
	require_once __DIR__ . "/../Nette/loader.php";
}

// Set debug options
Debugger::$strictMode = TRUE;
Debugger::$maxLen = 4096;

/**
 * Load and configure Nella Framework
 */
define('NELLA_FRAMEWORK', TRUE);
define('NELLA_FRAMEWORK_VERSION_ID', 20000); // v2.0.0

require_once __DIR__ . "/SplClassLoader.php";
Nella\SplClassLoader::getInstance(array(
	'Nella' => NELLA_FRAMEWORK_DIR,
	'Doctrine' => __DIR__ . "/../Doctrine",
	'Symfony' => __DIR__ . "/../Symfony",
))->register();

require_once __DIR__ . "/shortcuts.php";
require_once __DIR__ . "/Localization/shortcuts.php";
