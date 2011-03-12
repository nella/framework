<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

// Nella X-Powered
@header("X-Powered-By: Nette Framework with Nella"); // @ - headers may be sent

// Set debug options
Nette\Debug::$strictMode = TRUE;
Nette\Debug::$maxLen = 4096;

// Set better dependency injection container
Nette\Environment::setConfigurator(new Nella\DependencyInjection\ContextBuilder);
