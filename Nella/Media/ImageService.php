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
	const CACHE_DIR = "%imageCacheDir%";

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
			}

			return parent::delete($entity);
		} else {
			return parent::delete($entity);
		}
	}
}