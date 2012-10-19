<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Media\Model;

class FileTest extends \Nella\Testing\TestCase
{
	const PATH = 'logo.png';
	/** @var \Nella\Media\Model\File */
	private $file;

	public function setup()
	{
		$this->file = new \Nella\Media\Model\File(self::PATH);
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\Media\IFile', $this->file);
	}

	public function testGetPath()
	{
		$this->assertEquals(self::PATH, $this->file->getPath(), '->getPath()');
		$this->assertEquals(self::PATH, $this->file->path, '->path');
	}

	public function testGetContentType()
	{
		$this->assertEquals('image/png', $this->file->getContentType(), '->getContentType()');
		$this->assertEquals('image/png', $this->file->contentType, '->contentType');
	}

	public function testGetSlug()
	{
		$this->assertEquals('logo', $this->file->getSlug(), '->getSlug()');
		$this->assertEquals('logo', $this->file->slug, '->slug');
	}

	public function testGetFullSlug()
	{
		$this->assertEquals('logo', $this->file->getFullSlug(), '->getFullSlug()');
		$this->assertEquals('logo', $this->file->fullSlug, '->fullSlug');
	}
}
