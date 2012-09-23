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

use Doctrine\ORM\Mapping as orm,
	Nella\Media\IImageCacheStorage;

/**
 * Image facade
 *
 * @author	Patrik Votoček
 */
class ImageFacade extends BaseFacade implements \Nella\Media\Model\IImageDao
{
	/** @var \Nella\Media\IImageCacheStorage */
	protected $cacheStorage;

	/**
	 * @param string
	 * @return \Nella\Media\Doctrine\ImageEntity|NULL
	 */
	public function findOneByFullSlug($fullSlug)
	{
		return parent::findOneByFullSlug($fullSlug);
	}

	/**
	 * @param \Nella\Media\IImageCacheStorage
	 * @return ImageFacade
	 */
	public function setCacheStorage(IImageCacheStorage $cacheStorage)
	{
		$this->cacheStorage = $cacheStorage;
		return $this;
	}

	/**
	 * @param object
	 * @param bool
	 * @param string|\Nette\Http\FileUpload
	 */
	public function save($entity, $withoutFlush = self::FLUSH, $originalPath = NULL)
	{
		if ($entity->id !== NULL && $this->cacheStorage) {
			$cacheStorage = $this->cacheStorage;
			$entity->onFlush[] = function ($entity) use ($cacheStorage) {
				$cacheStorage->remove($entity);
			};
		}

		return parent::save($entity, $withoutFlush, $originalPath);
	}

	/**
	 * @param object
	 * @param bool
	 */
	public function remove($entity, $withoutFlush = self::FLUSH)
	{
		if ($entity->id !== NULL && $this->cacheStorage) {
			$cacheStorage = $this->cacheStorage;
			$entity->onFlush[] = function ($entity) use ($cacheStorage) {
				$cacheStorage->remove($entity);
			};
		}
		parent::remove($entity, $withoutFlush);
	}
}

