<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media;

/**
 * Image media type interface
 *
 * @author	Patrik Votoček
 *
 * @property-read string $imageType
 */
interface IImage extends IFile
{
	/**
	 * @return string
	 */
	public function getImageType();
}

