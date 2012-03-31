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
 * Resource image format interface
 *
 * @author	Patrik Votoček
 *
 * @property string $slug
 * @property int $width
 * @property int $height
 * @property int $flags
 * @property IImage|NULL $watermark
 * @property int $watermarkOpacity
 * @property int $watermarkPosition
 */
interface IImageFormat
{
	/** watermark positions */
	const POSITION_CENTER = 1,
		POSITION_TOP_LEFT = 2,
		POSITION_TOP_RIGHT = 3,
		POSITION_BOTTOM_LEFT = 4,
		POSITION_BOTTOM_RIGHT = 5;

	/**
	 * @return string
	 */
	public function getSlug();

	/**
	 * @return string
	 */
	public function getFullSlug();

	/**
	 * @return int	pixels
	 */
	public function getWidth();

	/**
	 * @return int	pixels
	 */
	public function getHeight();

	/**
	 * @return int
	 */
	public function getFlags();

	/**
	 * @return bool
	 */
	public function isCrop();

	/**
	 * @return IImage
	 */
	public function getWatermark();

	/**
	 * @return int
	 */
	public function getWatermarkOpacity();

	/**
	 * @return int
	 */
	public function getWatermarkPosition();
}
