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
	/** @var \Nella\Doctrine\Container */
	private $container;

	/**
	 * @param \Nella\Doctrine\Container
	 */
	public function __construct(\Nella\Doctrine\Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Performs an authentication
	 *
	 * @param array
	 * @return Identity
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$service = $this->container->getEntityService('Nella\Security\IdentityEntity');

		if (strpos($username, '@') !== FALSE) {
			$entity = $service->repository->findOneByEmail($username);
		} else {
			$entity = $service->repository->findOneByUsername($username);
		}

		if (empty($entity)) {
			throw new \Nette\Security\AuthenticationException("User with this username or email is not registered", self::IDENTITY_NOT_FOUND);
		}

		if ($entity->verifyPassword($password) == FALSE) {
			throw new \Nette\Security\AuthenticationException("Invalid password", self::INVALID_CREDENTIAL);
		}

		return new Identity($entity);
	}
}
