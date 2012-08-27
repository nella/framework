<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, 
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella;

/**
 * The Nella Framework
 *
 * @author	Patrik Votoček
 */
final class Framework
{
	/**Nella Framework version identification */
	const NAME = 'Nella Framework',
		VERSION = '2.0-dev',
		REVISION = '$WCREV$ released on $WCDATE$';

	/**
	 * Static class - cannot be instantiated.
	 */
	final public function __construct()
	{
		throw new \Nette\StaticClassException;
	}
}

