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
 * Custom identity - does not store data in session
 *
 * @author	Patrik Votoček
 * 
 * @property-read int $id
 * @property-read array $roles
 * @property-read IdentityDocument $document
 */
class Identity extends \Nette\Object implements \Nette\Security\IIdentity, \Serializable
{
	/** @var string */
	private $id;
	/** @var IdentityDocument */
	private $document;

	/**
	 * @param IdentityDocument
	 */
	public function __construct(IdentityDocument $document)
	{
		$this->document = $document;
		$this->id = $this->document->id;
	}

	/**
	  * Returns the ID of user.
	  *
	  * @return mixed
	  */
	public function getId()
	{
		return $this->document->id;
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 * @return array
	 */
	public function getRoles()
	{
		return array($this->document->role);
	}

	/**
	 * @return IdentityDocument
	 */
	public function getDocumet()
	{
		return $this->document;
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
	 * @throws InvalidStateException
	 */
	public function unserialize($serialized)
	{
		$this->id = unserialize($serialized);

		$documentManager = \Nette\Environment::getApplication()->context->getService('Doctrine\ODM\MongoDB\DocumentManager'); // @todo how to better DI?
		$service = new \Nella\Models\Service($documentManager);
		$this->document = $service->repository->find($this->id);

		if (!$this->document) {
			throw new \InvalidStateException("User with id {$this->id} not found");
		}
	}
}
