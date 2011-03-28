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
 * Image route
 * 
 * @author	Patrik Votoček
 */
class ImageRoute extends \Nette\Application\Route
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
	
	/**
	 * @param int
	 * @return \Nella\Models\ImageEntity
	 */
	protected function getImage($id)
	{
		$service = new \Nella\Models\Service($this->em, 'Nella\Media\ImageEntity');
		return $service->repository->find($id);
	}
}
