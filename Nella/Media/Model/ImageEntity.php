<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
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
