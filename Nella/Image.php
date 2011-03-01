<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella;

/**
 * Basic manipulation with images
 *
 * @author	Patrik Votoček
 */
class Image extends \Nette\Image
{
	/**
	 * @param int
	 * @param int
	 * @return Image
	 */
	public function resizeAndCrop($width, $height)
	{
		return $this->resize($width, $height, self::FILL | self::ENLARGE)->crop('50%', '50%', $width, $height);
	}
}