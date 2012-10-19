<?php
/**
 * Test: Nella\Media\Model\File
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Media\Model\FileTest
 */

namespace Nella\Tests\Media\Model;

use Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class FileTest extends \TestCase
{
	const PATH = 'logo.png';
	/** @var \Nella\Media\Model\File */
	private $file;

	public function setUp()
	{
		parent::setUp();
		$this->file = new \Nella\Media\Model\File(self::PATH);
	}

	public function testInstanceOf()
	{
		Assert::true($this->file instanceof \Nella\Media\IFile);
	}

	public function testGetPath()
	{
		Assert::equal(self::PATH, $this->file->getPath(), '->getPath()');
		Assert::equal(self::PATH, $this->file->path, '->path');
	}

	public function testGetContentType()
	{
		Assert::equal('image/png', $this->file->getContentType(), '->getContentType()');
		Assert::equal('image/png', $this->file->contentType, '->contentType');
	}

	public function testGetSlug()
	{
		Assert::equal('logo', $this->file->getSlug(), '->getSlug()');
		Assert::equal('logo', $this->file->slug, '->slug');
	}

	public function testGetFullSlug()
	{
		Assert::equal('logo', $this->file->getFullSlug(), '->getFullSlug()');
		Assert::equal('logo', $this->file->fullSlug, '->fullSlug');
	}
}
