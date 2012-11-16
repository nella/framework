<?php
/**
 * Test: Nella\Media\Model\Image
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
	Nella\Media\Model\Image;

require_once __DIR__ . '/../../../bootstrap.php';

class ImageTest extends \Tester\TestCase
{
	const PATH = 'logo.png';
	/** @var \Nella\Media\Model\Image */
	private $image;

	public function setUp()
	{
		parent::setUp();
		$this->image = new Image(self::PATH);
	}

	public function testInstanceOf()
	{
		Assert::true($this->image instanceof \Nella\Media\IFile, 'is instance of Nella\Media\IFile');
		Assert::true($this->image instanceof \Nella\Media\IImage, 'is instance of Nella\Media\IImage');
	}

	public function testGetPath()
	{
		Assert::equal(self::PATH, $this->image->getPath(), '->getPath()');
		Assert::equal(self::PATH, $this->image->path, '->path');
	}

	public function testGetContentType()
	{
		Assert::equal('image/png', $this->image->getContentType(), '->getContentType()');
		Assert::equal('image/png', $this->image->contentType, '->contentType');
	}

	public function testGetSlug()
	{
		Assert::equal('logo', $this->image->getSlug(), '->getSlug()');
		Assert::equal('logo', $this->image->slug, '->slug');
	}

	public function testGetFullSlug()
	{
		Assert::equal('logo', $this->image->getFullSlug(), '->getFullSlug()');
		Assert::equal('logo', $this->image->fullSlug, '->fullSlug');
	}

	public function testGetImageType()
	{
		Assert::equal('png', $this->image->getImageType(), '->getImageType()');
		Assert::equal('png', $this->image->imageType, '->imageType');
	}

	public function testInvalidFileType()
	{
		Assert::throws(function() {
			new Image('foo.zip');
		}, 'Nette\InvalidArgumentException');
	}
}

id(new ImageTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
