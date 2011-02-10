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
 * Simple authorizator implementation
 *
 * @author	Patrik Votoček
 */
class Authorizator extends \Nette\Security\Permission
{
	const ROLE = 'role';
	const RESOURCE = 'resource';
	const PRIVILEGE = 'privilege';
	
	/**
	 * @param \Doctrine\ODM\MongoDB\DocumentManager
	 */
	public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $documentManager)
	{
		$service = new \Nella\Models\Service($documentManager, 'Nella\Security\RoleDocument');
		$roles = $service->repository->findAll();
		
		foreach ($roles as $role) {
			$this->addRole($role->name);
			foreach ($role->permissions as $permission) {
				if (!$this->hasResource($permission->resource)) {
					$this->addResource($permission->resource);
				}
				
				$this->allow($role->name, $permission->resource, $permission->privilege);
			}
		}
	}
	
	/**
	 * @param string
	 * @param string
	 * @return array
	 */
	public static function parseAnnotations($class, $method = NULL)
	{
		if (strpos($class, '::') !== FALSE && !$method) {
			list($class, $method) = explode('::', $class);
		}

		$ref = new \Nette\Reflection\MethodReflection($class, $method);
		$cRef = new \Nette\Reflection\ClassReflection($class);
		$anntations = (array)$ref->getAnnotation('allowed');
		
		$role = isset($anntations['role']) ? $anntations['role'] : ($ref->hasAnnotation('role') ? $ref->getAnnotation('role') : NULL);
		$resource = isset($anntations['resource']) ? $anntations['resource'] : ($ref->hasAnnotation('resource') ? $ref->getAnnotation('resource') : ($cRef->hasAnnotation('resource') ? $cRef->getAnnotation('resource') : NULL));
		$privilege = isset($anntations['privilege']) ? $anntations['privilege'] : ($ref->hasAnnotation('privilege') ? $ref->getAnnotation('privilege') : NULL);
		
		return array(
			self::ROLE => $role, 
			self::RESOURCE => $resource, 
			self::PRIVILEGE => $privilege, 
		);
	}
}
