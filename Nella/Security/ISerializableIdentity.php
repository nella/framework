<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Security;

/**
 * Serializable identity entity interface
 *
 * @author	Patrik Votoček
 */
interface ISerializableIdentity extends \Nette\Security\IIdentity, \Serializable
{
	/**
	 * @return bool
	 */
	public function isLoaded();

	/**
	 * @param \Doctrine\ORM\EntityManager
	 */
	public function load(\Doctrine\ORM\EntityManager $em);
}