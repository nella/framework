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
 * Role entity
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 *
 * @entity(repositoryClass="Nella\Security\RoleRepository")
 * @table(name="acl_roles")
 * @hasLifecycleCallbacks
 *
 * @property-read array $children
 * @property string $name
 * @property RoleEntity|NULL $parent
 * @property-read array $permissions
 */
class RoleEntity extends \Nella\Models\Entity
{
	/**
	 * @column(length=128, unique=true)
	 * @var string
	 */
	private $name;
	/**
     * @manyToOne(targetEntity="RoleEntity", inversedBy="children")
     * @joinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
	 * @var RoleEntity
	 */
	private $parent;
	/**
     * @oneToMany(targetEntity="RoleEntity", mappedBy="parent", cascade={"all"})
	 * @var \Doctrine\Common\Collections\ArrayCollection
     */
	private $children;
	/**
	 * @oneToMany(targetEntity="PermissionEntity", mappedBy="role", cascade={"all"})
	 * @var array
	 */
	private $permissions;

	public function __construct()
	{
		parent::__construct();
		$this->permissions = new \Doctrine\Common\Collections\ArrayCollection;
		$this->children = new \Doctrine\Common\Collections\ArrayCollection;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string
	 * @return RoleEntity
	 */
	public function setName($name)
	{
		$name = trim($name);
		$this->name = $name == "" ? NULL : $name;
		return $this;
	}

	/**
	 * @return RoleEntity|NULL
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @param RoleEntity|NULL
	 * @return RoleEntity
	 */
	public function setParent($parent)
	{
		if (!($parent instanceof RoleEntity) && $parent !== NULL) {
			throw new \InvalidArgumentException("Parent muset be either an instance of Nella\Security\RoleEntity or null");
		}

		$this->parent = $parent;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getChildren()
	{
		return $this->children->toArray();
	}

	/**
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}
}