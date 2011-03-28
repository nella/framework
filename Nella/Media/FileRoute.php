<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Media;

/**
 * File route
 * 
 * @author	Patrik Votoček
 */
class FileRoute extends \Nette\Application\Route
{
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
	/**
	 * @param \Doctrine\ORM\EntityManager
	 */
	public function setEntityManager(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
	}
	
	protected function getFile($id)
	{
		$service = new \Nella\Models\Service($this->em, 'Nella\Media\FileEntity');
		return $service->repository->find($id); 
	}
}