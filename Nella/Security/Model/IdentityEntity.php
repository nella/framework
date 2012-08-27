<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Security\Model;

use Doctrine\ORM\Mapping as orm;

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
class IdentityEntity extends \Nette\Object implements \Nella\Security\ISerializableIdentity
{
	/**
	 * @orm\id
	 * @orm\generatedValue
	 * @orm\column(type="integer")
	 * @var int
	 */
	private $id;

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

	public function __construct()
	{
		$this->loaded = TRUE;
	}

	/**
	 * @param string
	 * @return string
	 */
	protected static function normalizeString($input)
	{
		$input = trim($input);
		return $input === '' ? NULL : $input;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

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
			$entity = $em->find(get_class($this), $this->getId());
			$entity->loaded = TRUE;
			return $entity;
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

