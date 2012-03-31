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
 * Query Object interface
 *
 * @author	Patrik Votoček
 */
interface IQueryObject
{
	/**
	 * @param IQueryable
	 * @return int
	 */
	public function count(IQueryable $broker);

	/**
	 * @param IQueryable
	 * @return array
	 */
	public function fetch(IQueryable $broker);

	/**
	 * @param IQueryable
	 * @return NULL
	 */
	public function fetchOne(IQueryable $broker);
}