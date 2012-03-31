<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
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