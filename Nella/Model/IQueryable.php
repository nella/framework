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
 * Queryable interface
 *
 * @author	Patrik Votoček
 */
interface IQueryable
{
	/**
	 * @param string|NULL
	 * @return \Doctrine\ORM\QueryBuilder|\Doctrine\CouchDB\View\AbstractQuery
	 */
	public function createQueryBuilder($alias);
}