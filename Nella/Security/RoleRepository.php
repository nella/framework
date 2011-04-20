<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Security;

/**
 * Role entity repository
 *
 * @author	Pavel Kučera
 */
class RoleRepository extends \Nella\Models\Repository
{
	/**
	 * Returns all role entities ordered by name
	 * 
	 * @param bool
	 * @return array
	 */
	public function findAllOrderedByName($desc = FALSE)
	{
		$qb = $this->createQueryBuilder('r')
			->orderBy('r.name', $desc ? 'DESC' : 'ASC');
		
		return $qb->getQuery()->getResult();
	}
}