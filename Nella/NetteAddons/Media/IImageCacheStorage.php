<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Media;

/**
 * Image cache media storage interface
 *
 * @author	Patrik Votoček
 */
interface IImageCacheStorage
{
	/**
	 * @param IImage
	 * @param IImageFormat
	 * @param string
	 * @return \Nette\Image|string
	 */
	public function load(IImage $image, IImageFormat $format, $type);

	/**
	 * @param IImage
	 * @param IImageFormat
	 * @param string
	 * @param \Nette\Image|string
	 */
	public function save(IImage $image, IImageFormat $format, $type, $from);

	/**
	 * @param IImage
	 */
	public function remove(IImage $image);

	/**
	 * @param IImageFormat|NULL
	 */
	public function clean(IImageFormat $format = NULL);
}
