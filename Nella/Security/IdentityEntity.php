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
 * @entity
 * @table(name="acl_users")
 * @service(class="Nella\Security\IdentityService")
 * @hasLifecycleCallbacks
 *
 * @property RoleEntity $role
 * @property string $lang
 */
class IdentityEntity extends \Nette\Object implements \Nella\Models\IEntity, \Nette\Security\IIdentity, \Serializable
{
	/**
	 * @id
	 * @generatedValue
	 * @column(type="integer")
	 */
	private $id;

	/**
	 * @manyToOne(targetEntity="RoleEntity", fetch="EAGER")
     * @joinColumn(name="role_id", referencedColumnName="id", nullable=false)
	 * @var RoleEntity
	 */
	private $role;
	/**
	 * @column(length=5)
	 * @var string
	 */
	private $lang;
	/**
	 * @internal
	 * @var bool
	 */
	private $loaded = FALSE;

	public function __construct()
	{

	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
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
	 * @internal
	 * @return array
	 */
	public function getRoles()
	{
		return array($this->getRole());
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
		$this->lang = $this->sanitizeString($lang);
		return $this;
	}

	/**
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->getId());
	}

	/**
	 * @param string
	 * @throws \Nette\InvalidStateException
	 */
	public function unserialize($serialized)
	{
		$this->id = unserialize($serialized);
		$this->loaded = FALSE;
	}

	/**
	 * @param \Nella\Doctrine\Container
	 * @return IdentityEntity
	 */
	public function load(\Nella\Doctrine\Container $container)
	{
		if (!$this->loaded) {
			$service = $container->getService(__CLASS__);
			$entity = $service->repository->find($this->getId());
			$entity->loaded = TRUE;
			return $entity;
		} else {
			return $this;
		}
	}

	/**
	 * @param string
	 * @return string
	 */
	protected function sanitizeString($s)
	{
		$s = trim($s);
		return $s === "" ? NULL : $s;
	}
}
