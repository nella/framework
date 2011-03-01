<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Media;

/**
 * Image media entity
 * 
 * @entity(repositoryClass="Nella\Models\Repository")
 * @table(name="media_images")
 * 
 * @author	Patrik Votoček
 */
class ImageEntity extends BaseFileEntity implements IImage
{
	/**
	 * @return \Nella\Image
	 */
	public function toImage()
	{
		return \Nella\Image::fromFile($this->getPath());
	}
}
