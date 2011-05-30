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
 * Model service interface
 *
 * @author	Patrik Votoček
 */
interface IService
{
	/**
	 * @param Container
	 * @param string
	 */
	public function __construct(Container $container, $entityClass);

	/**
	 * @return \Nette\Models\Container
	 */
	public function getContainer();

	/**
	 * @return string
	 */
	public function getEntityClass();

	/**
	 * @param array|\Traversable
	 * @return IEntity
	 */
	public function create($values);

	/**
	 * @param IEntity
	 * @param array|\Traversable
	 * @return IEntity
	 */
	public function update(IEntity $entity, $values);

	/**
	 * @param IEntity
	 * @return IEntity
	 */
	public function delete(IEntity $entity);
}
