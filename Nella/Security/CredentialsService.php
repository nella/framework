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
			$values['identity'] = $identityService->create($values, TRUE);

			$entity = parent::create($values);
			$em = $this->getEntityManager();
			$em->persist($entity);
			if (!$withoutFlush) {
				$em->flush();
			}
			return $entity;
		} catch (\PDOException $e) {
			$info = $e->errorInfo;
			if ($info[0] == 23000 && $info[1] == 1062) { // unique fail
				// @todo how to detect column name ?
				throw new \Nella\Models\DuplicateEntryException($e->getMessage(), NULL, $e);
			} elseif ($info[0] == 23000 && $info[1] == 1048) { // notnull fail
				// @todo convert table column name to entity column name
				$name = substr($info[2], strpos($info[2], "'") + 1);
				$name = substr($name, 0, strpos($name, "'"));
				throw new \Nella\Models\EmptyValueException($e->getMessage(), $name, $e);
			} else { // other fail
				throw new \Nella\Models\Exception($e->getMessage(), 0, $e);
			}
		}
	}
}
