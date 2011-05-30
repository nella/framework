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
 * Model container
 *
 * @author	Patrik Votoček
 */
class Container extends \Nella\FreezableObject
{
	/** @var string */
	protected $defaultServiceClass;
	/** @var array */
	protected $services = array();

	/**
	 * @param string
	 * @return Container
	 * @throws \Nette\InvalidArgumentException
	 */
	public function setDefaultServiceClass($class)
	{
		$this->updating();
		if (!class_exists($class)) {
			throw new \Nette\InvalidArgumentException("Class '$class' does not exist'");
		} elseif (!\Nette\Reflection\ClassType::from($class)->implementsInterface('Nella\Models\IService')) {
			throw new \Nette\InvalidArgumentException(
				"Service '$class' does not valid model service (must implements Nella\\Models\\IService)"
			);
		}

		$this->defaultServiceClass = $class;
		return $this;
	}


	/**
	 * @param string
	 * @return IService
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\InvalidStateException
	 */
	public function getService($entityClass)
	{
		if (isset($this->services[$entityClass])) {
			return $this->services[$entityClass];
		}

		if (!class_exists($entityClass)) {
			throw new \Nette\InvalidArgumentException("Class '$entityClass' does not exist'");
		} elseif (!\Nette\Reflection\ClassType::from($entityClass)->implementsInterface('Nella\Models\IEntity')) {
			throw new \Nette\InvalidArgumentException(
				"Entity '$entityClass' does not valid entity (must implements Nella\\Models\\IEntity)"
			);
		}

		$class = $this->defaultServiceClass;
		if (!class_exists($class)) {
			throw new \Nette\InvalidStateException("Service class '$class' does not exist'");
		}

		return $this->services[$entityClass] = new $class($this, $entityClass);
	}

	/**
	 * @param IService
	 * @return Container
	 */
	public function setService(IService $service)
	{
		$this->updating();
		$this->services[$service->getEntityClass()] = $service;
		return $this;
	}
}
