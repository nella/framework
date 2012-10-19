<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Mocks\Media;

class Format extends \Nette\Object implements \Nella\Media\IImageFormat
{
	public $slug;

	public function getSlug()
	{

	}

	public function getFullSlug()
	{
		return $this->slug;
	}

	public function getWidth()
	{
		return 100;
	}

	public function getHeight()
	{
		return 100;
	}

	public function getFlags()
	{

	}

	public function isCrop()
	{

	}

	public function getWatermark()
	{
		return NULL;
	}

	public function getWatermarkOpacity()
	{

	}

	public function getWatermarkPosition()
	{

	}
}