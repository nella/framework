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
 * Basic entity with ID
 *
 * @mappedSuperclass
 *
 * @author	Patrik Votoček
 *
 * @property-read int $id
 */
abstract class Entity extends \Nette\Object
{
	/**
	 * @id
	 * @generatedValue
	 * @column(type="integer")
	 */
	private $id;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
}