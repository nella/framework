<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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