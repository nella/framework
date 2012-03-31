<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Model;

/**
 * Query Executor interface
 *
 * @author	Patrik Votoček
 */
interface IQueryExecutor
{
	/**
	 * @param IQueryObject
	 * @return int
	 */
	public function count(IQueryObject $queryObject);

	/**
	 * @param IQueryObject
	 * @return array
	 */
	public function fetch(IQueryObject $queryObject);

	/**
	 * @param IQueryObject
	 * @return object|NULL
	 */
	public function fetchOne(IQueryObject $queryObject);
}