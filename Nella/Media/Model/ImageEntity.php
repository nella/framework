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
 * Image resoucre entity
 *
 * @orm\entity
 * @orm\table(name="media_images")
 *
 * @orm\inheritanceType("JOINED")
 * @orm\discriminatorColumn(name="type", type="string")
 * @orm\discriminatorMap({"base" = "ImageEntity"})
 *
 * @author	Patrik Votoček
 * 
 * @property-read string $imageType
 */
class ImageEntity extends BaseFileEntity implements \Nella\NetteAddons\Media\IImage
{	
	/**
	 * @return string
	 */
	public function getImageType()
	{
		$ext = \Nella\NetteAddons\Media\Helper::mimeTypeToExt($this->getContentType());
		return in_array($ext, array('png', 'jpg', 'gif')) ? $ext : 'jpg';
	}
}
