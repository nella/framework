<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media\Doctrine;

use Doctrine\ORM\Mapping as orm;

/**
 * Image format facade
 *
 * @author	Patrik Votoček
 */
class ImageFormatFacade extends \Nella\Model\Facade implements \Nella\Media\Model\IImageFormatDao
{
	/** @var \Nella\Media\IImageCacheStorage */
	protected $cacheStorage;

	/**
	 * @param \Nella\Media\IImageCacheStorage
	 * @return ImageFormatFacade
	 */
	public function setCacheStorage(\Nella\Media\IImageCacheStorage $cacheStorage)
	{
		$this->cacheStorage = $cacheStorage;
		return $this;
	}

	/**
	 * @param string
	 * @return \Nella\Media\Doctrine\ImageFormatEntity|NULL
	 */
	public function findOneByFullSlug($fullSlug)
	{
		list($id, $slug) = explode('-', $fullSlug, 2);
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
		if ($entity instanceof \Nella\Media\IImageFormat && $entity->id !== NULL && $this->cacheStorage) {
			$cacheStorage = $this->cacheStorage;
			$entity->onFlush[] = function ($entity) use ($cacheStorage) {
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
		if ($entity instanceof \Nella\Media\IImageFormat && $entity->id !== NULL && $this->cacheStorage) {
			$cacheStorage = $this->cacheStorage;
			$entity->onFlush[] = function ($entity) use ($cacheStorage) {
				$cacheStorage->clean($entity);
			};
		}

		return parent::remove($entity, $withoutFlush);
	}
}

