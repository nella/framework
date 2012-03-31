<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Doctrine;

use DoctrineExtensions\Paginate\Paginate,
	Nella\Model\IQueryable;

/**
 * Query Object
 *
 * @author	Patrik Votoček
 *
 * @property-read \Nette\Utils\Paginator|NULL $paginator
 */
class QueryObject extends \Nette\Object implements \Nella\Model\IQueryObject
{
	/** @var \Nette\Utils\Paginator */
	private $paginator;

	/**
	 * @param \Nette\Utils\Paginator
	 */
	public function __construct(\Nette\Utils\Paginator $paginator = NULL)
	{
		$this->paginator = $paginator;
	}

	/**
	 * @return \Nette\Utils\Paginator|NULL
	 */
	public function getPaginator()
	{
		return $this->paginator;
	}

	/**
	 * @param \Nella\Model\IQueryable
	 * @return \Doctrine\ORM\Query|Doctrine\CouchDB\View\AbstractQuery
	 */
	protected function doCreateQuery(IQueryable $broker)
	{
		return $broker->createQueryBuilder('qo')->getQuery();
	}

	/**
	 * @param IQueryable
	 * @return int
	 */
	public function count(IQueryable $broker)
	{
		return Paginate::getTotalQueryResults($this->doCreateQuery($broker));
	}

	/**
	 * @param \Nella\Model\IQueryable
	 * @return \Doctrine\Common\Collections\Collection|array
	 */
	public function fetch(IQueryable $broker)
	{
		$query = $this->doCreateQuery($broker);

		if ($this->paginator) { // Paginate
			$query = Paginate::getPaginateQuery($query, $this->paginator->getOffset(), $this->paginator->getLength());
		}

		try{
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return array();
		}
	}

	/**
	 * @param \Nella\Model\IQueryable
	 * @return \Nella\Doctrine\Entity|object|NULL
	 */
	public function fetchOne(IQueryable $broker)
	{
		$query = $this->doCreateQuery($broker);

		$query->setMaxResults(1);

		try{
			return $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return NULL;
		}
	}
}