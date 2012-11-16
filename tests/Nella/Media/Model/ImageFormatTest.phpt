<?php
/**
 * Test: Nella\Media\Model\ImageFormat
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Media\Model;

use Tester\Assert,
	Nella\Media\IImageFormat;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Media/Image.php';

class ImageFormatTest extends \Tester\TestCase
{
	/** @var \Nella\Media\Model\ImageFormat */
	private $format;

	public function setUp()
	{
		parent::setUp();
		$this->format = new \Nella\Media\Model\ImageFormat('default', 800, 600);
	}

	public function testInstanceOf()
	{
		Assert::true($this->format instanceof IImageFormat);
	}

	public function testGetSlug()
	{
		Assert::equal('default', $this->format->getSlug(), '->getSlug()');
		Assert::equal('default', $this->format->slug, '->slug');
	}

	public function testGetFullSlug()
	{
		Assert::equal('default', $this->format->getFullSlug(), '->getFullSlug()');
		Assert::equal('default', $this->format->fullSlug, '->fullSlug');
	}

	public function testGetWidth()
	{
		Assert::equal(800, $this->format->getWidth(), '->getWidth()');
		Assert::equal(800, $this->format->width, '->width');
	}

	public function testGetHeight()
	{
		Assert::equal(600, $this->format->getHeight(), '->getHeight()');
		Assert::equal(600, $this->format->height, '->height');
	}

	public function testGetFlags()
	{
		Assert::equal(0, $this->format->getFlags(), '->getFlags()');
		Assert::equal(0, $this->format->flags, '->flags');
	}

	public function testIsCrop()
	{
		Assert::false($this->format->isCrop(), '->getCrop()');
		Assert::false($this->format->crop, '->crop');
	}

	public function testWatermarkNone()
	{
		Assert::null($this->format->getWatermark(), '->getWatermark()');
		Assert::null($this->format->watermark, '->watermark');

		Assert::null($this->format->getWatermarkPosition(), '->getWatermarkOpacity()');
		Assert::null($this->format->watermarkOpacity, '->watermarkOpacity');

		Assert::null($this->format->getWatermarkPosition(), '->getWatermarkPosition()');
		Assert::null($this->format->watermarkPosition, '->watermarkPosition');
	}

	public function testWatermark()
	{
		$image = new \Nella\Mocks\Media\Image;
		$this->format->setWatermark($image);

		Assert::true($this->format->getWatermark() instanceof \Nella\Media\IImage, 'insatnce of Nella\Media\IImage');
		Assert::equal($image, $this->format->getWatermark(), '->getWatermark()');

		Assert::equal(0, $this->format->getWatermarkOpacity(), '->getWatermarkOpacity()');
		Assert::equal(0, $this->format->watermarkOpacity, '->watermarkOpacity');

		Assert::equal(IImageFormat::POSITION_CENTER, $this->format->getWatermarkPosition(), '->getWatermarkPosition()');
		Assert::equal(IImageFormat::POSITION_CENTER, $this->format->watermarkPosition, '->watermarkPosition');
	}
}

id(new ImageFormatTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
