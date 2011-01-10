<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Tools;

/**
 * Basic logger interface
 *
 * @author	Patrik Votoček
 */
interface ILogger
{
	/**#@+ Message levels */
	const INFO = 1;
	const WARNING = 2;
	const ERROR = 3;
	const FATAL = 4;
	const DEBUG = 9;
	/**#@-*/
	
	/**
	 * @param string
	 * @param int	message priority level
	 */
	public function logMessage($message, $level = self::ERROR);
}