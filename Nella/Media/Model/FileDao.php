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
 * File DAO
 *
 * @author	Patrik Votoček
 */
class FileDao extends \Nella\Doctrine\Dao implements \Nella\NetteAddons\Media\Model\IFileDao
{
	/** @var \Nella\NetteAddons\Media\IStorage */
	protected $storage;
	
	/**
	 * @param \Nella\NetteAddons\Media\IStorage
	 * @return ImageDao
	 */
	public function setStorage(\Nella\NetteAddons\Media\IStorage $storage)
	{
		$this->storage = $storage;
		return $this;
	}

	/**
	 * @param string
	 * @return \Nella\Media\Model\FileEntity|NULL
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
	 * @param string|\Nette\Http\FileUpload
	 */
	public function save($entity, $withoutFlush = self::FLUSH, $originalPath = NULL)
	{
		if ($entity->id === NULL && $this->storage) {
			if (!$originalPath) {
				throw new \Nette\InvalidStateException('Source path must be defined');
			}
			$storage = $this->storage;
			$entity->onFlush[] = function($entity) use($storage, $originalPath) {
				$storage->save($entity, $originalPath);
			};
		}
		
		return parent::save($entity, $withoutFlush);
	}
	
	/**
	 * @param object
	 * @param bool
	 */
	public function delete($entity, $withoutFlush = self::FLUSH)
	{
		if ($entity->id !== NULL && $this->storage) {
			$storage = $this->storage;
			$entity->onFlush[] = function($entity) use($storage) {
				$storage->remove($entity);
			};
		}
		parent::delete($entity, $withoutFlush);
	}
}
