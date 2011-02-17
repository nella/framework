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
 * Basic action logger interface
 *
 * @author	Patrik Votoček
 */
interface IActionLogger
{
	/** Message action */
	const CREATE = "create", 
		READ = "read", 
		UPDATE = "update", 
		DELETE = "delete", 
		OTHER = "other";
	
	/**
	 * @param string	module name
	 * @param string
	 * @param string
	 * @param user Nette\Security\IIdentity
	 */
	public function logAction($module, $action = self::OTHER, $message = "", \Nette\Security\IIdentity $user = NULL);
}