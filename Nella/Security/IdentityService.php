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
 * Identity model service
 *
 * @author	Patrik Votoček
 */
class IdentityService extends \Nella\Doctrine\Service
{
	/**
	 * @param array|\Traversable
	 * @param bool
	 * @return \Nella\Models\IEntity
	 * @throws \Nette\InvalidArgumentException
	 */
	public function create($values, $withoutFlush = FALSE)
	{
		try {
			if (!$values['role'] instanceof \Nella\Security\RoleEntity) {
				$roleService = $this->getContainer()->getService('Nella\Security\RoleEntity');
				$values['role'] = $roleService->repository->find($values['role']);
			}

			$entity = parent::create($values, TRUE);
			$em = $this->getEntityManager();
			$em->persist($entity);
			if (!$withoutFlush) {
				$em->flush();
			}
			return $entity;
		} catch (\PDOException $e) {
			$this->processPDOException($e);
		}
	}
}
