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
 * Identity entity
 *
 * @author	Patrik Votoček
 * 
 * @entity(repositoryClass="Nella\Models\Repository")
 * @table(name="acl_users")
 * @hasLifecycleCallbacks
 * 
 * @property string $username
 * @property string $email
 * @property string $password
 * @property RoleEntity $role
 * @property string $lang
 * @property string $realname
 */
class IdentityEntity extends \Nella\Models\Entity
{
	const PASSWORD_DELIMITER = "$";
	
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
	 * @manyToOne(targetEntity="RoleEntity")
     * @joinColumn(name="role_id", referencedColumnName="id")
	 * @var RoleEntity
	 */
	private $role;
	/**
	 * @column(length=5)
	 * @var string
	 */
	private $lang;
	/**
	 * @column(length=256)
	 * @var string
	 */
	private $realname;

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
		$username = trim($username);
		$this->username = $username === "" ? NULL : $username;
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
		$email = trim($email);
		$this->email = $email == "" ? NULL : $email;
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
		
		list($algo, $salt, $hash) = explode(self::PASSWORD_DELIMITER, $this->password);
		return array('algo' => $algo, 'salt' => $salt, 'hash' => $hash);
	}

	/**
	 * @param string
	 * @param string
	 * @return IdentityEntity
	 */
	public function setPassword($password, $algo = "sha256")
	{
		$salt = \Nette\String::random();
		$this->password = $algo . self::PASSWORD_DELIMITER . $salt . self::PASSWORD_DELIMITER . hash($algo, $salt . $password);
		return $this;
	}
	
	/**
	 * @param string plaintext password
	 * @return bool
	 */
	public function verifyPassword($password)
	{
		list($algo, $salt, $hash) = explode(self::PASSWORD_DELIMITER, $this->password);
		if (hash($algo, $salt . $password) == $hash) {
			return TRUE;
		}
		
		return FALSE;	
	}

	/**
	 * @return RoleEntity
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @param RoleEntity
	 * @return IdentityEntity
	 */
	public function setRole(RoleEntity $role)
	{
		$this->role = $role;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getLang()
	{
		return $this->lang;
	}

	/**
	 * @param string
	 * @return IdentityEntity
	 */
	public function setLang($lang)
	{
		$lang = trim($lang);
		$this->lang = $lang == "" ? NULL : $lang;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRealname()
	{
		return $this->realname;
	}

	/**
	 * @param string
	 * @return IdentityEntity
	 */
	public function setRealname($realname)
	{
		$realname = trim($realname);
		$this->realname = $realname == "" ? NULL : $realname;
		return $this;
	}
	
	/**
	 * @prePersist
	 * @preUpdate
	 * 
	 * @throws \Nella\Models\EmptyValuesException
	 * @throws \Nella\Models\InvalidFormatException
	 * @throws \Nella\Models\DuplicateEntryException
	 */
	public function check()
	{
		$em = \Nette\Environment::getApplication()->context->getService('Doctrine\ORM\EntityManager');
		$service = new \Nella\Models\Service($em, 'Nella\Security\IdentityEntity');
		if (!$service->repository->isColumnUnique($this->id, 'username', $this->username)) {
			throw new \Nella\Models\DuplicateEntryException('username', "Username value must be unique");
		}
	}
}
