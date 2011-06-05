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
		$path .= "/" . time() . "_" . \Nette\Utils\Strings::random() . "." . $ext;
		return $path;
	}

	/**
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
			$list[] = array($this->create(array('path' => $this->generatePath($item)), TRUE), $item);
		}

		try {
			if (!$withoutFlush) {
				$this->getEntityManager()->flush();
			}

			$dir = $this->getContainer()->expand(static::STORAGE_DIR);

			$collection = array();
			foreach ($list as $item) {
				$collection[] = $item[1]->move($dir . "/" . $item[0]->getPath());
			}

			return $collection;
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
		$class = get_class($entity);
		if ($class == 'Nella\Media\FileEntity' || $class == 'Nella\Media\ImageEntity') {
			$path = $this->getContainer()->expand(static::STORAGE_DIR);
			$path .= "/" . $entity->path;
			@unlink($path);

			return parent::delete($entity);
		} else {
			return parent::delete($entity);
		}
	}
}