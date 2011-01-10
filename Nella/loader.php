<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

require_once __DIR__ . "/hacks.php";

/**
 * Load and configure Nella Framework
 */
define('NELLA', TRUE);
define('NELLA_DIR', __DIR__);
define('NELLA_VERSION_ID', 20000); // v2.0.0

Nette\Debug::$strictMode = TRUE;
