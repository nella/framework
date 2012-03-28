<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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