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
 * Custom identity - does not store data in session
 *
 * @author	Patrik Votoček
 *
 * @property-read int $id
 * @property-read array $roles
 * @property-read IdentityEntity $entity
 */
class Identity extends \Nette\Object implements \Nette\Security\IIdentity, \Serializable
{
	/** @var string */
	private $id;
	/** @var IdentityEntity */
	private $entity;

	/**
	 * @param IdentityEntity
	 */
	public function __construct(IdentityEntity $entity)
	{
		$this->entity = $entity;
		$this->id = $this->entity->id;
	}

	/**
	  * Returns the ID of user.
	  *
	  * @return mixed
	  */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 * @return array
	 */
	public function getRoles()
	{
		return array($this->entity->role);
	}

	/**
	 * @return IdentityEntity
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->id);
	}

	/**
	 * @param string
	 * @throws \Nette\InvalidStateException
	 */
	public function unserialize($serialized)
	{
		$this->id = unserialize($serialized);

		$dc = \Nette\Environment::getContext()->getService('doctrineContainer'); // @todo how to better DI?
		
		$service = $dc->getEntityService('Nella\Security\IdentityEntity');
		$this->entity = $service->repository->find($this->id);

		if (!$this->entity) {
			throw new \Nette\InvalidStateException("User with id {$this->id} not found");
		}
	}
}
