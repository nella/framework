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
 * Role document
 *
 * @author	Patrik Votoček
 * 
 * @document(repositoryClass="Nella\Models\Repository")
 * @hasLifecycleCallbacks
 * 
 * @property string $name
 * @property-read array $permissions
 */
class RoleDocument extends \Nella\Models\Document
{
	/** 
	 * @string
	 * @index(unique=true, order="asc")
	 * @var string
	 */
	private $name;
	/**
	 * @embedMany(targetDocument="PermissionDocument")
	 * @var array
	 */
	private $permissions;
	
	public function __construct()
	{
		parent::__construct();
		$this->permissions = new \Doctrine\Common\Collections\ArrayCollection;
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
	 * @return RoleDocument
	 */
	public function setName($name)
	{
		$name = trim($name);
		$this->name = $name == "" ? NULL : $name;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->permissions;
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
		$service = $this->getModelService('Nella\Security\Models\RoleService');
		parent::check();
		if ($this->name === NULL) {
			throw new \Nella\Models\EmptyValuesException('name', "Name value must be non empty string");	
		}
		
		if (!$service->repository->isColumnUnique($this->id, 'name', $this->name)) {
			throw new \Nella\Models\DuplicateEntryException('name', "Name value must be unique");	
		}		
	}
}