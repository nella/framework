<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Security\Model;

/**
 * Indentity
 *
 * @orm\entity
 * @orm\table(name="acl_users")
 *
 * @author    Patrik Votoček
 *
 * @property-read array $roles
 * @property string $displayName
 */
class IdentityEntity extends \Nella\Doctrine\Entity implements \Nella\Security\ISerializableIdentity
{
	/**
	 * @orm\column
	 * @var string
	 */
	private $displayName;
	/**
	 * @internal
	 * @var bool
	 */
	private $loaded = FALSE;

	/**
	 * Returns a list of roles that the user is a member of
	 *
	 * @todo
	 * @return array
	 */
	public function getRoles()
	{
		return array();
	}

	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->displayName;
	}

	/**
	 * @param string
	 * @return IdentityEntity
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = static::normalizeString($displayName);
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
	 * @return bool
	 */
	public function isLoaded()
	{
		return $this->loaded;
	}

	/**
	 * @param \Doctrine\ORM\EntityManager
	 * @return IdentityEntity
	 */
	public function load(\Doctrine\ORM\EntityManager $em)
	{
		if (!$this->loaded) {
			$em->refresh($this);
			$this->loaded = TRUE;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getDisplayName();
	}
}