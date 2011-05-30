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
 * Identity credentials entity
 *
 * @entity
 * @table(name="acl_user_credentials")
 * @service(class="Nella\Security\CredentialsService")
 *
 * @author	Patrik Votoček
 *
 * @property IdentityEntity $identity
 * @property string $username
 * @property string $email
 * @property string $password
 */
class CredentialsEntity extends \Nella\Doctrine\Entity
{
	const PASSWORD_DELIMITER = "$";

	/**
	 * @oneToOne(targetEntity="Nella\Security\IdentityEntity", fetch="EAGER")
     * @joinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 * @var IdentityEntity
	 */
	private $identity;
	/**
	 * @column(length=128, unique=true)
	 * @var string
	 */
	private $username;
	/**
	 * @column(length=256)
	 * @var string
	 */
	private $email;
	/**
	 * @column(length=256)
	 * @var string
	 */
	private $password;

	/**
	 * @return IdentityEntity
	 */
	public function getIdentity()
	{
		return $this->identity;
	}

	/**
	 * @param IdentityEntity
	 * @return ProfileEntity
	 */
	public function setIdentity(IdentityEntity $identity)
	{
		$this->identity = $identity;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string
	 * @return IdentityEntity
	 */
	public function setUsername($username)
	{
		$this->username = $this->sanitizeString($username);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string
	 * @return IdentityEntity
	 */
	public function setEmail($email)
	{
		$this->email = $this->sanitizeString($email);
		return $this;
	}

	/**
	 * @param bool return as string
	 * @return string
	 */
	public function getPassword($string = TRUE)
	{
		if ($string || !$this->password) {
			return $this->password;
		}

		return explode(self::PASSWORD_DELIMITER, $this->password);
	}

	/**
	 * @param string
	 * @param string
	 * @return IdentityEntity
	 */
	public function setPassword($password, $algo = "sha256")
	{
		$salt = \Nette\Utils\Strings::random();

		$this->password = $algo . self::PASSWORD_DELIMITER;
		$this->password .= $salt . self::PASSWORD_DELIMITER;
		$this->password .= hash($algo, $salt . $password);

		return $this;
	}

	/**
	 * @param string plaintext password
	 * @return bool
	 */
	public function verifyPassword($password)
	{
		list($algo, $salt, $hash) = $this->getPassword(FALSE);
		if (hash($algo, $salt . $password) == $hash) {
			return TRUE;
		}

		return FALSE;
	}
}
