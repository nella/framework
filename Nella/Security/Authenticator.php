<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Security;

/**
 * Simple authenticator implementation
 *
 * @author	Patrik Votoček
 */
class Authenticator extends \Nette\Object implements \Nette\Security\IAuthenticator
{
	/** @var \Nella\Security\Model\CredentialsDao */
	private $model;

	/**
	 * @param \Nella\Security\Model\CredentialsDao
	 */
	public function __construct(\Nella\Security\Model\CredentialsDao $model)
	{
		$this->model = $model;
	}

	/**
	 * Performs an authentication
	 *
	 * @param array
	 * @return IdentityEntity
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$entity = $this->model->findOneByEmailOrUsername($username);

		if (empty($entity)) {
			throw new \Nette\Security\AuthenticationException("User with this username or email is not registered", self::IDENTITY_NOT_FOUND);
		}

		if ($entity->verifyPassword($password) == FALSE) {
			throw new \Nette\Security\AuthenticationException("Invalid password", self::INVALID_CREDENTIAL);
		}

		return $entity->identity;
	}
}