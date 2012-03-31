<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Media\Model;

use Doctrine\ORM\Mapping as orm;

/**
 * Image format DAO
 *
 * @author	Patrik Votoček
 */
class ImageFormatDao extends \Nella\Doctrine\Dao implements \Nella\NetteAddons\Media\Model\IImageFormatDao
{
	/** @var \Nella\NetteAddons\Media\IImageCacheStorage */
	protected $cacheStorage;

	/**
	 * @param \Nella\NetteAddons\Media\IImageCacheStorage
	 * @return ImageDao
	 */
	public function setCacheStorage(\Nella\NetteAddons\Media\IImageCacheStorage $cacheStorage)
	{
		$this->cacheStorage = $cacheStorage;
		return $this;
	}

	/**
	 * @param string
	 * @return \Nella\Media\Model\ImageFormatEntity|NULL
	 */
	public function findOneByFullSlug($slug)
	{
		list($id, $fullSlug) = explode('-', $slug, 2);
		$entity = $this->repository->find($id);
		if ($entity && $entity->getFullSlug() == $fullSlug) {
			return $entity;
		}

		return NULL;
	}


	/**
	 * @param object
	 * @param bool
	 * @param string
	 */
	public function save($entity, $withoutFlush = self::FLUSH, $originalPath = NULL)
	{
		if ($entity instanceof \Nella\NetteAddons\Media\IImageFormat && $entity->id !== NULL && $this->cacheStorage) {
			$cacheStorage = $this->cacheStorage;
			$entity->onFlush[] = function($entity) use($cacheStorage) {
				$cacheStorage->clean($entity);
			};
		}

		return parent::save($entity, $withoutFlush);
	}

	/**
	 * @param object
	 * @param bool
	 */
	public function remove($entity, $withoutFlush = self::FLUSH)
	{
		if ($entity instanceof \Nella\NetteAddons\Media\IImageFormat && $entity->id !== NULL && $this->cacheStorage) {
			$cacheStorage = $this->cacheStorage;
			$entity->onFlush[] = function($entity) use($cacheStorage) {
				$cacheStorage->clean($entity);
			};
		}

		return parent::remove($entity, $withoutFlush);
	}
}
