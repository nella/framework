<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Model;

/**
 * Dao interface
 *
 * @author	Patrik Votoček
 */
interface IDao
{
	const FLUSH = FALSE,
		NO_FLUSH = TRUE;
	
	/**
	 * @param object
	 * @param bool 
	 */
	public function save($entity, $withoutFlush = self::FLUSH);
	
	/**
	 * @param object
	 * @param bool
	 */
	public function remove($entity, $withoutFlush = self::FLUSH);

	/**
	 * @param mixed
	 * @return object
	 */
	public function find($id);

	/**
	 * @return array
	 */
	public function findAll();

	/**
	 * @param array
	 * @return object|NULL
	 */
	public function findOneBy(array $criteria);

	/**
	 * @param array
	 * @param array|NULL
	 * @param int|NULL
	 * @param int|NULL
	 * @return array
	 */
	public function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL);
}