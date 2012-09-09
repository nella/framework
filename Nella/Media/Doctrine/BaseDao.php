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
 * Base file / image DAO
 *
 * @author	Patrik Votoček
 */
abstract class BaseDao extends \Nella\Model\Facade
{
	/** @var \Nella\Media\IStorage */
	protected $storage;

	/**
	 * @param \Nella\Media\IStorage
	 * @return ImageDao
	 */
	public function setStorage(\Nella\Media\IStorage $storage)
	{
		$this->storage = $storage;
		return $this;
	}

	/**
	 * @param string
	 * @return \Nella\Media\Doctrine\FileEntity|\Nella\Media\Doctrine\ImageEntity|NULL
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
	 * @param string|\Nette\Http\FileUpload
	 */
	public function save($entity, $withoutFlush = self::FLUSH, $originalPath = NULL)
	{
		if ($entity->id === NULL && $this->storage) {
			if (!$originalPath) {
				throw new \Nette\InvalidStateException('Source path must be defined');
			}
			$storage = $this->storage;
			$entity->onFlush[] = function ($entity) use ($storage, $originalPath) {
				$storage->save($entity, $originalPath);
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
		if ($entity->id !== NULL && $this->storage) {
			$storage = $this->storage;
			$entity->onFlush[] = function ($entity) use ($storage) {
				$storage->remove($entity);
			};
		}
		parent::remove($entity, $withoutFlush);
	}
}

