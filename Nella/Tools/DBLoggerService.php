<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Tools;

/**
 * Database logger service
 *
 * @author	Patrik Votoček
 */
class DBLoggerService extends \Nella\Models\Service implements ILogger
{
	/**
	 * @param string
	 * @param int	message priority level
	 */
	public function logMessage($message, $level = self::ERROR)
	{
		$entity = new DBLoggerEntity;
		$entity->setMessage($message)->setLevel($level);
		
		$this->persist($entity);
		$this->flush();
	}
}