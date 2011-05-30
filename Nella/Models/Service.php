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
 * Model service
 *
 * @author	Patrik Votoček
 */
abstract class Service extends \Nette\Object
{
	/** @var Container */
	private $container;
	/** @var string */
	private $entityClass;

	/**
	 * @param Container
	 * @param string
	 * @return Service
	 */
	public function __construct(Container $container, $entityClass)
	{
		if (!class_exists($entityClass)) {
			throw new \Nette\InvalidArgumentException("Entity '$entityClass' does not exist");
		} elseif (!\Nette\Reflection\ClassType::from($entityClass)->implementsInterface('Nella\Models\IEntity')) {
			throw new \Nette\InvalidArgumentException(
				"Entity '$entityClass' does not valid entity (must implements Nella\\Models\\IEntity)"
			);
		}

		$this->container = $container;
		$this->entityClass = $entityClass;
	}

	final public function getContainer()
	{
		return $this->container;
	}

	final public function getEntityClass()
	{
		return $this->entityClass;
	}

	/**
	 * @return mixed
	 */
	protected function createEntityPrototype()
	{
		$class = $this->getEntityClass();
		return new $class;
	}

	/**
	 * @param IEntity
	 * @param array|\Traversable
	 * @throws \Nette\InvalidArgumentException
	 */
	protected function fillData(IEntity $entity, $values)
	{
		if (!is_array($values) && !$values instanceof \Traversable) {
			throw new \Nette\InvalidArgumentException("Values must be array or Traversable");
		}

		foreach ($values as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($entity, $method)) {
				$entity->$method($value);
			}
		}
	}

	/**
	 * @param array|\Traversable
	 * @return IEntity
	 * @throws \Nette\InvalidArgumentException
	 */
	public function create($values)
	{
		if (!is_array($values) && !$values instanceof \Traversable) {
			throw new \Nette\InvalidArgumentException("Values must be array or Traversable");
		}

		$entity = $this->createEntityPrototype();
		$this->fillData($entity, $values);
		return $entity;
	}

	/**
	 * @param IEntity
	 * @param array|\Traversable
	 * @return IEntity
	 * @throws \Nette\InvalidArgumentException
	 */
	public function update(IEntity $entity, $values)
	{
		$this->fillData($entity, $values);
		return $entity;
	}
}
