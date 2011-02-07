<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella;
 
require_once __DIR__ . "/hacks.php";
require_once __DIR__ . "/shortcuts.php";

/**
 * Load and configure Nella Framework
 */
define('NELLA_FRAMEWORK', TRUE);
define('NELLA_FRAMEWORK_DIR', __DIR__);
define('NELLA_FRAMEWORK_VERSION_ID', 20000); // v2.0.0

/** #@+ Base control flash messages class */
const FLASH_SUCCESS = "success";
const FLASH_ERROR = "error";
const FLASH_INFO = "info";
const FLASH_WARNING = "warning";
/** #@- */

\Nette\Debug::$strictMode = TRUE;
\Nette\Debug::$maxDepth = 32;
\Nette\Debug::$maxLen = 4096;
