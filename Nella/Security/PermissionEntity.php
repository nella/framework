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
 * Persmission entity
 * 
 * @entity(repositoryClass="Nella\Models\Repository")
 * @table(name="permissions")
 *
 * @author	Patrik Votoček
 * 
 * @property string $resource
 * @property string $privilege
 */
class PermissionEntity extends \Nella\Models\Entity
{
	/**
	 * @manyToOne(targetEntity="RoleEntity", inversedBy="permissions")
     * @joinColumn(name="role_id", referencedColumnName="id")
	 * @var mixed
	 */
	private $role;
	/**
	 * @column(length=128)
	 * @var string
	 */
	private $resource;
	/**
	 * @column(length=128)
	 * @var string
	 */
	private $privilege;
	
	/**
	 * @return string
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
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
}
