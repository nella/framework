<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Media\Model;

use Doctrine\ORM\Mapping as orm;

/**
 * Image DAO
 *
 * @author	Patrik Votoček
 */
class ImageDao extends FileDao implements \Nella\NetteAddons\Media\Model\IImageDao
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
	 * @param object
	 * @param bool
	 * @param string|\Nette\Http\FileUpload
	 */
	public function save($entity, $withoutFlush = self::FLUSH, $originalPath = NULL)
	{
		if ($entity->id !== NULL && $this->cacheStorage) {
			$cacheStorage = $this->cacheStorage;
			$entity->onFlush[] = function($entity) use($cacheStorage) {
				$cacheStorage->remove($entity);
			};
		}
		
		return parent::save($entity, $withoutFlush, $originalPath);
	}
	
	/**
	 * @param object
	 * @param bool
	 */
	public function delete($entity, $withoutFlush = self::FLUSH)
	{
		if ($entity->id !== NULL && $this->cacheStorage) {
			$cacheStorage = $this->cacheStorage;
			$entity->onFlush[] = function($entity) use($cacheStorage) {
				$cacheStorage->remove($entity);
			};
		}
		parent::delete($entity, $withoutFlush);
	}
}
