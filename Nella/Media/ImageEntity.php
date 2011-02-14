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
 * Image media entity
 * 
 * @entity(repositoryClass="Nella\Models\Repository")
 * 
 * @author	Patrik Votoček
 */
class ImageEntity extends BaseFileEntity implements IImage
{
	/**
	 * @return \Nella\Imgage
	 */
	public function toImage()
	{
		return \Nella\Image::fromFile($this->getPath());
	}
}
