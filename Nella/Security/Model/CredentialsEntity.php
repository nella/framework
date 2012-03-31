<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Security\Model;

use Doctrine\ORM\Mapping as orm;

/**
 * Identity credentials entity
 *
 * @orm\entity
 * @orm\table(name="acl_credentials")
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
	 * @orm\oneToOne(targetEntity="IdentityEntity", fetch="EAGER")
	 * @orm\joinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 * @var IdentityEntity
	 */
	private $identity;
	/**
	 * @orm\column(unique=true)
	 * @var string
	 */
	private $username;
	/**
	 * @orm\column
	 * @var string
	 */
	private $email;
	/**
	 * @orm\column
	 * @var string
	 */
	private $password;

	/**
	 * @param \Nella\Security\Model\IdentityEntity
	 */
	public function __construct(IdentityEntity $identity)
	{
		parent::__construct();
		$this->identity = $identity;
	}

	/**
	 * @return IdentityEntity
	 */
	public function getIdentity()
	{
		return $this->identity;
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
		$this->username = self::normalizeString($username);
		if (!$this->getId() && !$this->identity->displayName) {
			$this->identity->displayName = $this->username;
		}
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
		$this->email = self::normalizeString($email);
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