<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

require_once __DIR__ . '/shortcuts.php';

require_once __DIR__ . '/SplClassLoader.php';
$map = array(
	'Nella' => __DIR__,
);
Nella\SplClassLoader::getInstance($map)->register();
