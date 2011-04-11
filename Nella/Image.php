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
	
	/**
	 * @param string
	 * @return int
	 */
	public static function extToType($type)
	{
		switch ($type) {
			case 'png':
				return static::PNG;
				break;
			case 'gif':
				return static::GIF;
				break;
			default:
				return static::JPEG;
				break;
		}
	}
	
	/**
	 * @param int
	 * @return string
	 */
	public static function typeToExt($type)
	{
		switch ($type) {
			case static::PNG:
				return 'png';
				break;
			case static::GIF:
				return 'gif';
				break;
			default:
				return 'jpg';
				break;
		}
	}
	
	/**
	 * Outputs image to browser.
	 * @param  int  image type
	 * @param  int  quality 0..100 (for JPEG and PNG)
	 * @return bool TRUE on success or FALSE on failure.
	 */
	public function send($type = self::JPEG, $quality = NULL)
	{
		if (is_string($type)) {
			$type = self::extToType($type);
		}
		
		return parent::send($type, $quality);
	}
}