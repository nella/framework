<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Media;

use Nette\Utils\Strings;

/**
 * Base media service
 *
 * @author	Patrik Votoček
 */
abstract class Service extends \Nella\Doctrine\Service
{
	/**
	 * @param \Nette\Http\FileUpload
	 * @param string|NULL
	 * @return string
	 */
	public function generatePath(\Nette\Http\FileUpload $upload, $path = "")
	{
		$ext = pathinfo($upload->getName(), PATHINFO_EXTENSION);
		$path .= "/" . time() . "_" . Strings::random() . "." . $ext;
		return $path;
	}
	
	/**
	 * @param \Nette\Http\FileUpload
	 * @param bool
	 * @return BaseFileEntity
	 */
	protected function createFromUpload(\Nette\Http\FileUpload $upload, $withoutFlush = FALSE)
	{
		$path = $this->generatePath($upload);
		$slug = pathinfo($path, PATHINFO_BASENAME) . '_' . Strings::webalize(pathinfo($upload->name, PATHINFO_BASENAME));
		$entity = $this->create(array('path' => $path, 'slug' => $slug), $withoutFlush);
		$upload->move($entity->getPath(TRUE));
		return $entity;
	}

	/**
	 * @deprecated
	 * @param array|\Traversable
	 * @param bool
	 * @return \Nella\Models\IEntity
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nella\Models\Exception
	 * @throws \Nella\Models\EmptyValueException
	 * @throws \Nella\Models\DuplicateEntryException
	 */
	public function createFromUploadCollection($collection, $withoutFlush = FALSE)
	{
		if (!is_array($collection) && !$collection instanceof \Traversable) {
			throw new \Nette\InvalidArgumentException("Collection must be array or Traversable");
		}

		$list = array();
		foreach ($collection as $item) {
			if (!$item instanceof \Nette\Http\FileUpload) {
				throw new \Nette\InvalidStateException("Collection must be collection of Nette\\Http\\FileUpload");
			}
			$list[] = $this->createFromUpload($item, FALSE);
		}

		try {
			if (!$withoutFlush) {
				$this->getEntityManager()->flush();
			}

			return $list;
		} catch (\PDOException $e) {
			$this->processPDOException($e);
		}
	}
	
	/**
	 * @param array|\Traversable|\Nette\Http\FileUpload
	 * @param bool
	 * @return BaseFileEntity
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nella\Models\Exception
	 * @throws \Nella\Models\EmptyValueException
	 * @throws \Nella\Models\DuplicateEntryException
	 */
	public function create($values, $withoutFlush = FALSE)
	{
		try {
			if ($values instanceof \Nette\Http\FileUpload) {
				return $this->createFromUpload($values, $withoutFlush);
			}
			
			return parent::create($values, $withoutFlush);
		} catch (\PDOException $e) {
			$this->processPDOException($e);
		}
	}

	/**
	 * @param \Nella\Media\BaseFileEntity
	 * @return \Doctrine\ORM\EntityManager
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nella\Models\Exception
	 * @throws \Nella\Models\EmptyValueException
	 * @throws \Nella\Models\DuplicateEntryException
	 */
	public function delete(\Nella\Models\IEntity $entity)
	{
		if ($entity instanceof BaseFileEntity) {
			@unlink($entity->getPath(TRUE));

			return parent::delete($entity);
		} else {
			return parent::delete($entity);
		}
	}
}