<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\NetteAddons\Media\Model;

use Nella\NetteAddons\Media\IImageFormat;

class ImageFormatTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\NetteAddons\Media\Model\ImageFormat */
	private $format;

	public function setup()
	{
		$this->format = new \Nella\NetteAddons\Media\Model\ImageFormat('default', 800, 600);
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\IImageFormat', $this->format);
	}

	public function testGetSlug()
	{
		$this->assertEquals('default', $this->format->getSlug(), '->getSlug()');
		$this->assertEquals('default', $this->format->slug, '->slug');
	}

	public function testGetFullSlug()
	{
		$this->assertEquals('default', $this->format->getFullSlug(), '->getFullSlug()');
		$this->assertEquals('default', $this->format->fullSlug, '->fullSlug');
	}

	public function testGetWidth()
	{
		$this->assertEquals(800, $this->format->getWidth(), '->getWidth()');
		$this->assertEquals(800, $this->format->width, '->width');
	}

	public function testGetHeight()
	{
		$this->assertEquals(600, $this->format->getHeight(), '->getHeight()');
		$this->assertEquals(600, $this->format->height, '->height');
	}

	public function testGetFlags()
	{
		$this->assertEquals(0, $this->format->getFlags(), '->getFlags()');
		$this->assertEquals(0, $this->format->flags, '->flags');
	}

	public function testIsCrop()
	{
		$this->assertFalse($this->format->isCrop(), '->getCrop()');
		$this->assertFalse($this->format->crop, '->crop');
	}

	public function testWatermarkNone()
	{
		$this->assertNull($this->format->getWatermark(), '->getWatermark()');
		$this->assertNull($this->format->watermark, '->watermark');

		$this->assertNull($this->format->getWatermarkPosition(), '->getWatermarkOpacity()');
		$this->assertNull($this->format->watermarkOpacity, '->watermarkOpacity');

		$this->assertNull($this->format->getWatermarkPosition(), '->getWatermarkPosition()');
		$this->assertNull($this->format->watermarkPosition, '->watermarkPosition');
	}

	public function testWatermark()
	{
		$image = $this->getMock('Nella\NetteAddons\Media\IImage');
		$this->format->setWatermark($image);

		$this->assertInstanceOf('Nella\NetteAddons\Media\IImage', $this->format->getWatermark(), 'insatnce of Nella\NetteAddons\Media\IImage');
		$this->assertEquals($image, $this->format->getWatermark(), '->getWatermark()');

		$this->assertEquals(0, $this->format->getWatermarkOpacity(), '->getWatermarkOpacity()');
		$this->assertEquals(0, $this->format->watermarkOpacity, '->watermarkOpacity');

		$this->assertEquals(IImageFormat::POSITION_CENTER, $this->format->getWatermarkPosition(), '->getWatermarkPosition()');
		$this->assertEquals(IImageFormat::POSITION_CENTER, $this->format->watermarkPosition, '->watermarkPosition');
	}
}
