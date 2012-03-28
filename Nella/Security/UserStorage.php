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
 * User strorage
 *
 * @author	Patrik Votoček
 */
class UserStorage extends \Nette\Http\UserStorage
{
	/** @var \Doctrine\ORM\EntityManager */
	private $em;

	/**
	 * @param \Nette\Http\Session
	 * @param \Doctrine\ORM\EntityManager
	 */
	public function  __construct(\Nette\Http\Session $sessionHandler, \Doctrine\ORM\EntityManager $em)
	{
		parent::__construct($sessionHandler);
		$this->em = $em;
	}

	/**
	 * Returns and initializes $this->sessionSection
	 *
	 * @return \Nette\Http\SessionSection
	 */
	protected function getSessionSection($need)
	{
		$section = parent::getSessionSection($need);
		if (!$section) {
			return $section;
		}

		$identity = $section->identity;
		if ($identity instanceof ISerializableIdentity && !$identity->isLoaded()) {
			$identity->load($this->em);
		}

		return $section;
	}
}