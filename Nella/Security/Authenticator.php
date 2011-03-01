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
 * Simple authenticator implementation
 *
 * @author	Patrik Votoček
 */
class Authenticator extends \Nette\Object implements \Nette\Security\IAuthenticator
{
	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;
	
	/**
	 * @param \Doctrine\ORM\EntityManager
	 */
	public function __construct(\Doctrine\ORM\EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	/**
	 * Performs an authentication
	 *
	 * @param array
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$service = new \Nella\Models\Service($this->entityManager, 'Nella\Security\IdentityEntity');
		
		if (strpos($username, '@') !== FALSE) {
			$entity = $service->repository->findOneByEmail($username);	
		} else {
			$entity = $service->repository->findOneByUsername($username);	
		}
		
		if (empty($entity)) {
			throw new \Nette\Security\AuthenticationException("User with this username is not registred", self::IDENTITY_NOT_FOUND);
		}
		
		if ($entity->verifyPassword($password) == FALSE) {
			throw new \Nette\Security\AuthenticationException("Invalid password", self::INVALID_CREDENTIAL);
		}
  
		return new Identity($entity);
	}
}
