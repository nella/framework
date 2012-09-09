<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Media\Model;

use Nella\Media\Model\Image;

class ImageTest extends \Nella\Testing\TestCase
{
	const PATH = 'logo.png';
	/** @var \Nella\Media\Model\Image */
	private $image;

	public function setup()
	{
		$this->image = new Image(self::PATH);
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\Media\IFile', $this->image, 'is instance of Nella\Media\IFile');
		$this->assertInstanceOf('Nella\Media\IImage', $this->image, 'is instance of Nella\Media\IImage');
	}

	public function testGetPath()
	{
		$this->assertEquals(self::PATH, $this->image->getPath(), '->getPath()');
		$this->assertEquals(self::PATH, $this->image->path, '->path');
	}

	public function testGetContentType()
	{
		$this->assertEquals('image/png', $this->image->getContentType(), '->getContentType()');
		$this->assertEquals('image/png', $this->image->contentType, '->contentType');
	}

	public function testGetSlug()
	{
		$this->assertEquals('logo', $this->image->getSlug(), '->getSlug()');
		$this->assertEquals('logo', $this->image->slug, '->slug');
	}

	public function testGetFullSlug()
	{
		$this->assertEquals('logo', $this->image->getFullSlug(), '->getFullSlug()');
		$this->assertEquals('logo', $this->image->fullSlug, '->fullSlug');
	}

	public function testGetImageType()
	{
		$this->assertEquals('png', $this->image->getImageType(), '->getImageType()');
		$this->assertEquals('png', $this->image->imageType, '->imageType');
	}

	/**
	 * @expectedException Nette\InvalidArgumentException
	 */
	public function testInvalidFileType()
	{
		new Image('foo.zip');
	}
}
