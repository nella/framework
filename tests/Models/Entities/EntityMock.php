<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Models;

/**
 * @entity
 */
class EntityMock extends \Nella\Models\Entity
{
	/**
	 * @param int
	 */
	public function __construct($id = NULL)
	{
		$ref = new \Nette\Reflection\PropertyReflection('Nella\Models\Entity', 'id');
		$ref->setAccessible(TRUE);
		$ref->setValue($this, $id);
		$ref->setAccessible(FALSE);
	}
}