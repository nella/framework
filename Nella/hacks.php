<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

// Nella X-Powered
@header("X-Powered-By: Nette Framework with Nella"); // @ - headers may have been sent

// Set debug options
Nette\Diagnostics\Debugger::$strictMode = TRUE;
Nette\Diagnostics\Debugger::$maxLen = 4096;

// Set better dependency injection container
$configurator = new Nella\Configurator;
$configurator->onCreateContainer[] = function(\Nette\DI\Container $container) {
	// Run RobotLoader
	$container->getService('Nette\Loaders\RobotLoader');
	
	// Init configuration
	$container->setParam('prefixies', array(
		'App\\', 
		'Nella\\', 
	));
	$container->setParam('templates', array(
		$container->getParam('appDir'), 
		NELLA_FRAMEWORK_DIR, 
	));
	
	// Init multilple file upload listener
	Nella\Forms\Controls\MultipleFileUpload::register(
		$container->getService('Nette\Web\IHttpRequest'), 
		$container->getParam('uploadDir')
	);
	
	// Load panels
	$container->getService('callbackPanel');
	$container->getService('versionPanel');
};
Nette\Environment::setConfigurator($configurator);

