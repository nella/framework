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
 * Persmission entity
 *
 * @entity
 * @table(name="acl_permissions")
 *
 * @author	Patrik Votoček
 *
 * @property RoleEntity $role
 * @property string $resource
 * @property string $privilege
 * @property bool $allow
 */
class PermissionEntity extends \Nella\Doctrine\Entity
{
	/**
	 * @manyToOne(targetEntity="RoleEntity", inversedBy="permissions")
     * @joinColumn(name="role_id", referencedColumnName="id")
	 * @var mixed
	 */
	private $role;
	/**
	 * @column(length=128,nullable=true)
	 * @var string
	 */
	private $resource;
	/**
	 * @column(length=128,nullable=true)
	 * @var string
	 */
	private $privilege;
	/**
	 * @column(type="boolean")
	 * @var bool
	 */
	private $allow;

	/**
	 * @return RoleEntity
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @internal
	 * @param RoleEntity
	 * @return PermissionEntity
	 */
	public function setRole(RoleEntity $role)
	{
		$this->role = $role;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @internal
	 * @param string
	 * @return PersmissionEntity
	 */
	public function setResource($resource)
	{
		$resource = trim($resource);
		$this->resource = $resource == "" ? NULL : $resource;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrivilege()
	{
		return $this->privilege;
	}

	/**
	 * @param string
	 * @return PersmissionEntity
	 */
	public function setPrivilege($privilege)
	{
		$privilege = trim($privilege);
		$this->privilege = $privilege == "" ? NULL : $privilege;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAllow()
	{
		return $this->allow;
	}

	/**
	 * @param bool
	 * @return PermissionEntity
	 */
	public function setAllow($allow)
	{
		$this->allow = $allow;
		return $this;
	}
}
