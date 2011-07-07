<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine\Listeners;

/**
 * Versions storage object for repository
 *
 * @entity
 * @table(name="versions")
 *
 * @author	Patrik Votoček
 *
 * @property-read \DateTime $created
 * @property-read int $entityId
 * @property-read string $entityData
 * @property-read string $entityClass
 */
class VersionEntity extends \Nella\Doctrine\Entity
{
	/**
	 * @column(type="datetimetz")
	 * @var \DateTime
	 */
	private $created;
	/**
	 * @column(type="integer")
	 * @var int
	 */
	private $entityId;
	/**
	 * @column(type="text")
	 * @var string
	 */
	private $entityData;
	/**
	 * @column(length=256)
	 * @var string
	 */
	private $entityClass;

	/**
	 * @param \Nella\Doctrine\IVersionableEntity
	 */
	public function __construct(\Nella\Doctrine\IVersionableEntity $entity)
	{
		$snapshot = $entity->takeSnapshot();
		if ($snapshot === NULL || !is_string($snapshot)) {
			throw new \Nette\InvalidStateException(
				"Snapshot data (\$entity->takeSnapshot() return) must be string"
			);
		}
		$this->created = new \DateTime;
		$this->entityId = $entity->getId();
		$this->entityData = $snapshot;
		$this->entityClass = get_class($entity);
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @return int
	 */
	public function getEntityId()
	{
		return $this->entityId;
	}

	/**
	 * @return string
	 */
	public function getEntityData()
	{
		return $this->entityData;
	}

	/**
	 * @return string
	 */
	public function getEntityClass()
	{
		return $this->entityClass;
	}
}
