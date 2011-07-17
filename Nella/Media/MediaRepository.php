<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Media;

/**
 * Media entity repository
 *
 * @author	Patrik Votoček
 */
class MediaRepository extends \Nella\Doctrine\Repository
{
	/**
	 * @param string
	 * @return int
	 */
	public function fetchIdBySlug($slug)
	{
		try {
			return $this->createQueryBuilder('r')->select("r.id")->where("r.slug = :slug")->setParameter('slug', $slug)
				->getQuery()->getSingleScalarResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			if (!(bool) \Nette\Utils\Strings::match($slug, '/^-?[0-9]+$/')) { // if numeric use fallback
				return NULL;
			}
			
			try { // fallback for NULLable slug - use ID
				return (bool) $this->createQueryBuilder('r')->select("count(r)")->where("r.id = :id")->setParameter('id', $slug)
				->getQuery()->getSingleScalarResult() ?  $slug : NULL;
			} catch (\Doctrine\ORM\NoResultException $e) {
				return NULL;
			}
		}
	}
}
