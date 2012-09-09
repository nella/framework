<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Model;

/**
 * Basic entity repository
 *
 * @author	Patrik Votoček
 */
class Repository extends \Nella\Doctrine\Repository implements IQueryable, IQueryExecutor
{
	/**
	 * @param \Nella\Model\IQueryObject
	 * @return int
	 */
	public function count(IQueryObject $queryObject)
	{
		return $queryObject->count($this);
	}

	/**
	 * @param \Nella\Model\IQueryObject
	 * @return array
	 */
	public function fetch(IQueryObject $queryObject)
	{
		return $queryObject->fetch($this);
	}

	/**
	 * @param \Nella\Model\IQueryObject
	 * @return object|NULL
	 */
	public function fetchOne(IQueryObject $queryObject)
	{
		return $queryObject->fetchOne($this);
	}
}

