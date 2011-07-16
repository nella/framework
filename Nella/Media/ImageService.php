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
 * Image service
 *
 * @author	Patrik Votoček
 */
class ImageService extends Service
{
	const STORAGE_DIR = "%storageDir%/images";
	const CACHE_DIR = "%imageCache%";
	
	/**
	 * @param array|\Traversable|\Nette\Http\FileUpload
	 * @param bool
	 * @return ImageEntity
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nella\Models\Exception
	 * @throws \Nella\Models\EmptyValueException
	 * @throws \Nella\Models\DuplicateEntryException
	 */
	public function create($values, $withoutFlush = FALSE)
	{
		try {
			if (!$values instanceof \Nette\Http\FileUpload && isset($values['image']) && $values['image'] instanceof \Nette\Http\FileUpload) {
				return $this->createFromUpload($values['file']);
			}
			
			return parent::create($values, $withoutFlush);
		} catch (\PDOException $e) {
			$this->processPDOException($e);
		}
	}

	/**
	 * @param \Nella\Models\IEntity
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function delete(\Nella\Models\IEntity $entity)
	{
		$class = get_class($entity);
		if ($class == 'Nella\Media\ImageEntity') {
			$path = $this->getContainer()->context->expand(static::CACHE_DIR);
			if (file_exists($path)) {
				$id = $entity->id;
				$files = \Nette\Utils\Finder::findFiles($id . ".jpg", $id . ".png", $id . ".gif")->from($path);

				foreach($files as $file) {
					if (file_exists($file->getRealPath())) {
						@unlink($file->getRealPath());
					}
				}
				
				$slug = $entity->slug;
				$files = \Nette\Utils\Finder::findFiles($slug . ".jpg", $slug . ".png", $slug . ".gif")->from($path);

				foreach($files as $file) {
					if (file_exists($file->getRealPath())) {
						@unlink($file->getRealPath());
					}
				}
			}

			return parent::delete($entity);
		} else {
			return parent::delete($entity);
		}
	}
}
