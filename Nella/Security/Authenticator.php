<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Security;

/**
 * Simple authenticator implementation
 *
 * @author	Patrik Votoček
 */
class Authenticator extends \Nette\Object implements \Nette\Security\IAuthenticator
{
	/** @var \Doctrine\ODM\MongoDB\DocumentManager */
	private $documentManager;
	
	/**
	 * @param \Doctrine\ODM\MongoDB\DocumentManager
	 */
	public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $documentManager)
	{
		$this->documentManager = $documentManager;
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
		$service = new AuthenticatorService($this->documentManager, 'Nella\Security\Identity');
		
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