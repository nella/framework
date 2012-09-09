<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Security\Model;

/**
 * Credetioals Facade
 *
 * @author    Patrik Votoček
 */
class CredentialsFacade extends \Nella\Model\Facade
{
	/**
	 * @param array
	 * @return CredentialsEntity
	 */
	public function create(array $values = array())
	{
		$values['identity'] = new IdentityEntity;
		$ref = $this->em->getClassMetadata($this->repository->getClassName())->getReflectionClass();
		if (empty($values)) {
			return $ref->newInstance();
		}
		return $ref->newInstanceArgs(array($values['identity']));
	}

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

	/**
	 * @param string
	 * @return CredentialsEntity|NULL
	 */
	public function findOneByUsername($username)
	{
		return $this->repository->findOneByUsername($username);
	}

	/**
	 * @param string
	 * @return CredentialsEntity|NULL
	 */
	public function findOneByEmail($email)
	{
		return $this->repository->findOneByEmail($email);
	}

	/**
	 * @param object
	 * @param bool
	 */
	public function save($entity, $withoutFlush = self::FLUSH)
	{
		if (isset($entity->identity)) {
			parent::save($entity->identity, self::NO_FLUSH);
		}
		return parent::save($entity, $withoutFlush);
	}
}

