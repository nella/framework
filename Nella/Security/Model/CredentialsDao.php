<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Security\Model;

/**
 * Credetioals DAO
 *
 * @author    Patrik Votoček
 */
class CredentialsDao extends \Nella\Doctrine\Dao
{
	/**
	 * @param string
	 * @return CredentialsEntity|NULL
	 */
	public function findOneByEmailOrUsername($credential)
	{
		if (strpos($credential, '@') !== FALSE) {
			return $this->repository->findOneByEmail($credential);
		} else {
			return $this->repository->findOneByUsername($credential);
		}
	}
}