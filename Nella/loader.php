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
if (!defined('NETTE') && !file_exists(__DIR__ . "/../Nette/loader.php")) {
	die('You must load Nette Framework first');
}

/**
 * Load and configure Nella Framework
 */
define('NELLA_FRAMEWORK', TRUE);
define('NELLA_FRAMEWORK_VERSION_ID', 20000); // v2.0.0

require_once __DIR__ . "/SplClassLoader.php";
Nella\SplClassLoader::getInstance(array(
	'Nella' => __DIR__,
))->register();

require_once __DIR__ . "/shortcuts.php";
