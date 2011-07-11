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
 * Credentials model service
 *
 * @author	Patrik Votoček
 */
class CredentialsService extends \Nella\Doctrine\Service
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
			$identityService = $this->getContainer()->getService('Nella\Security\IdentityEntity');
			$values['displayName'] = $values['username'];
			$values['identity'] = $identityService->create($values, TRUE);

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
