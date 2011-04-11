<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella;

// Load and init Nette Framework
if (!defined('NETTE')) {
	require_once __DIR__ . "/../Nette/loader.php";
}

/**
 * Load and configure Nella Framework
 */
define('NELLA_FRAMEWORK', TRUE);
define('NELLA_FRAMEWORK_DIR', __DIR__);
define('NELLA_FRAMEWORK_VERSION_ID', 20000); // v2.0.0

// Set file upload temp dir
ini_set('upload_tmp_dir', TEMP_DIR . "/uploaded");
// Set session dir
ini_set('session.save_path', TEMP_DIR . "/sessions");

/** #@+ Base control flash messages class */
const FLASH_SUCCESS = "success";
const FLASH_ERROR = "error";
const FLASH_INFO = "info";
const FLASH_WARNING = "warning";
/** #@- */

require_once __DIR__ . "/NellaLoader.php";
NellaLoader::getInstance()->register();

require_once __DIR__ . "/hacks.php";
require_once __DIR__ . "/shortcuts.php";
require_once __DIR__ . "/Localization/shortcuts.php";
