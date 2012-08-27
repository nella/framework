<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
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
	public function __construct(\Nette\Http\Session $sessionHandler, \Doctrine\ORM\EntityManager $em)
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
			$section->identity = $identity->load($this->em);
		}

		return $section;
	}
}

