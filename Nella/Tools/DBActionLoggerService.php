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
 * Database action logger service
 *
 * @author	Patrik Votoček
 */
class DBActionLoggerService extends \Nella\Models\Service implements IActionLogger
{
	/**
	 * @param string	module name
	 * @param string
	 * @param string
	 * @param user Nette\Security\IIdentity
	 */
	public function logAction($module, $action = self::OTHER, $message = "", \Nette\Security\IIdentity $user = NULL)
	{
		if ($user) {
			$user = $user->entity;
		}
		
		$entity = new DBActionLogger;
		$entity->setModule($module)->setAction($action)->setMessage($message)->setUser($user);
		
		$this->persist($entity);
		$this->flush();
	}
}
