<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Models;

/**
 * Base model service
 *
 * @author	Patrik Votoček
 * 
 * @property-read \Doctrine\ORM\EntityManager $entityManager
 * @property-read string $entityClass
 * @property-read \Doctrine\ORM\EntityRepository $repository
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
 */
class Service extends \Nette\Object
{
	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;
	/** @var string */
	private $entityClass;
	
	/**
	 * @param \Doctrine\ORM\EntityManager
	 * @param string
	 */
	public function __construct(\Doctrine\ORM\EntityManager $entityManager, $entityClass = NULL)
	{
		$this->entityManager = $entityManager;
		$this->entityClass = $entityClass;
	}
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}
	
	/**
	 * @return string
	 */
	public function getEntityClass()
	{
		return $this->entityClass;
	}
	
	/**
	 * @param string
	 * @return \Doctrine\ORM\EntityRepository
	 * @throws \InvalidArgumentException
	 */
	public function getEntityRepository($entityClass = NULL)
	{
		$entityClass = $entityClass ?: $this->getEntityClass();
		if (empty($entityClass)) {
			throw new \InvalidArgumentException("Default entity name is not set, you must set entity name in param");
		}
		
		return $this->getEntityManager()->getRepository($entityClass);
	}
	
	/**
	 * @param string
	 * @return \Doctrine\ORM\ClassMetadata
	 * @throws \InvalidArgumentException
	 */
	public function getClassMetadata($entityClass = NULL)
	{
		$entityClass = $entityClass ?: $this->getEntityClass();
		if (empty($entityClass)) {
			throw new \InvalidArgumentException("Default entity name is not set, you must set entity name in param");
		}
		
		return $this->getEntityManager()->getClassMetadata($entityClass);
	}
	
	/**
	 * @param BaseEntity
	 * @return BaseEntity
	 */
	public function persist(BaseEntity $entity)
	{
		$this->getEntityManager()->persist($entity);
		return $entity;
	}
	
	/**
	 * @param BaseEntity
	 * @return BaseEntity
	 */
	public function remove(BaseEntity $entity)
	{
		$this->getEntityManager()->remove($entity);
		return $entity;
	}
	
	public function flush()
	{
		return $this->getEntityManager()->flush();
	}
}