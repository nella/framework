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
	 * @param \Nella\Media\IEntity
	 * @return \Doctrine\ORM\EntityManager
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