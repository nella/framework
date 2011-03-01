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
 * Media image format interface
 *
 * @author	Patrik Votoček
 */
interface IFormat
{
	/** watermark positions */
	const POSITION_CENTER = 1, 
		POSITION_TOP_LEFT = 2, 
		POSITION_TOP_RIGHT = 3, 
		POSITION_BOTTOM_LEFT = 4, 
		POSITION_BOTTOM_RIGHT = 5;
	
	/**
	 * @return int	pixels
	 */
	public function getWidth();
	
	/**
	 * @param int
	 * @return IFormat
	 */
	public function setWidth($width);
	
	/**
	 * @return int	pixels
	 */
	public function getHeight();
	
	/**
	 * @param int
	 * @return IFormat
	 */
	public function setHeight($height);
	
	/**
	 * @return bool
	 */
	public function isCrop();
	
	/**
	 * @param bool
	 * @return IFormat
	 */
	public function setCrop($crop);
	
	/**
	 * @return IImage
	 */
	public function getWatermark();
	
	/**
	 * @param IImage
	 * @return IFormat
	 */
	public function setWatermark(IImage $watermark);
	
	/**
	 * @return int
	 */
	public function getWatermarkOpacity();
	
	/**
	 * @param int
	 * @return IFormat
	 */
	public function setWatermarkOpacity($opacity);
	
	/**
	 * @return int
	 */
	public function getWatermarkPosition();
	
	/**
	 * @param int
	 * @return IFormat
	 */
	public function setWatermarkPosition($position);
	
	/**
	 * @param IImage
	 * @return \Nella\Image
	 */
	public function process(IImage $image);
}
