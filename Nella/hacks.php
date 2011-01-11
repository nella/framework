<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

// Load and init Nette
if (!defined('NETTE')) {
	require_once __DIR__ . "/../Nette/loader.php";
}

// Nella X-Powered
@header("X-Powered-By: Nette Framework with Nella"); // @ - headers may be sent

// Set config.neon as config file
require_once __DIR__ . "/Configurator.php";
require_once __DIR__ . "/Config/ConfigAdapterNeon.php";
Nette\Config\Config::registerExtension('neon', 'Nella\Config\ConfigAdapterNeon');
Nette\Environment::setConfigurator(new Nella\Configurator);

// Load panels
require_once __DIR__ . "/Panels/Callback.php";
Nella\Panels\Callback::register();
require_once __DIR__ . "/Panels/Version.php";
Nella\Panels\Version::register();