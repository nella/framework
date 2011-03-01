<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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
		throw new \LogicException("Cannot instantiate static class " . get_called_class());
	}
}