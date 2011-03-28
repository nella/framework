<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Models;

/**
 * @entity
 */
class UserableEntityMock extends \Nella\Models\Entity
{
	/**
	 * @creator
	 * @manyToOne(targetEntity="Nella\Security\IdentityEntity")
	 * @joinColumn(name="user_id", referencedColumnName="id")
	 * @var \Nella\Security\IdentityEntity
	 */
	private $creator;
	/**
	 * @editor
	 * @manyToOne(targetEntity="Nella\Security\IdentityEntity")
	 * @joinColumn(name="user_id", referencedColumnName="id")
	 * @var \Nella\Security\IdentityEntity
	 */
	private $editor = NULL;
	
	/**
	 * @return \Nella\Security\IdentityEntity
	 */
	public function getCreator()
	{
		return $this->creator;
	}
	
	/**
	 * @return \Nella\Security\IdentityEntity
	 */
	public function getEditor()
	{
		return $this->editor;
	}
	
	public function clean()
	{
		$this->editor = $this->creator = new \Nella\Security\IdentityEntity;
	}
}
