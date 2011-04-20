<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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
	 * @throws \Nette\InvalidArgumentException
	 */
	public function getRepository($entityClass = NULL)
	{
		$entityClass = $entityClass ?: $this->getEntityClass();
		if (empty($entityClass)) {
			throw new \Nette\InvalidArgumentException("Default entity name is not set, you must set entity name in param");
		}
		
		return $this->getEntityManager()->getRepository($entityClass);
	}
	
	/**
	 * @param string
	 * @return \Doctrine\ORM\ClassMetadata
	 * @throws \Nette\InvalidArgumentException
	 */
	public function getClassMetadata($entityClass = NULL)
	{
		$entityClass = $entityClass ?: $this->getEntityClass();
		if (empty($entityClass)) {
			throw new \Nette\InvalidArgumentException("Default entity name is not set, you must set entity name in param");
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
	
	/**
	 * @param BaseEntity
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function save($entity)
	{
		$this->persist($entity);
		return $this->flush();
	}
	
	/**
	 * @param BaseEntity
	 */
	public function getEntityPrototype()
	{
		$class = $this->getEntityClass();
		return new $class;
	}
	
	/**
	 * @param BaseEntity
	 * @param array
	 */
	public function setEntityData($entity, $data)
	{
		$ref = new \Nette\Reflection\ClassType(get_class($entity));
		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				$method = "set" . \Nette\Utils\Strings::firstUpper($key);
				if ($ref->hasMethod($method)) {
					$entity->$method($value);
				}
			} // @todo implements collections
		}
	}
	
	/**
	 * @param array
	 * @return BaseEntity
	 */
	public function createEntity($data)
	{
		$entity = $this->getEntityPrototype();
		$this->setEntityData($entity, $data);
		return $entity;
	}
}