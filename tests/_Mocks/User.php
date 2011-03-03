<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Mocks;

class User extends \Nette\Web\User
{
	public function __construct()
	{
		$ref = new \Nette\Reflection\PropertyReflection('Nella\Models\Entity', 'id');
		$ref->setAccessible(TRUE);
		$entity = new \Nella\Security\IdentityEntity;
		$ref->setValue($entity, 1);
		$ref->setAccessible(FALSE);
		$entity->setLang('es');
		$identity = new \Nella\Security\Identity($entity);
		
		$ref = new \Nette\Reflection\PropertyReflection('Nette\Web\User', 'session');
		$ref->setAccessible(TRUE);
		$ref->setValue($this, (object) array('authenticated' => TRUE, 'identity' => $identity));
		$ref->setAccessible(FALSE);
	}
	
	/*public function logoutMock()
	{
		$ref = new \Nette\Reflection\PropertyReflection('Nette\Web\User', 'session');
		$ref->setAccessible(TRUE);
		$ref->setValue($this, (object) array('authenticated' => FALSE, 'reason' => NULL));
		$ref->setAccessible(FALSE);
	}*/
	
	public function isAllowed($resource = NULL, $privilege = NULL)
	{
		return FALSE;
	}
}
