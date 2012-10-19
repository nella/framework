<?php
/**
 * Test: Nella\Media\Storages\File
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Media\Storages\FileTest
 */

namespace Nella\Tests\Media\Storages;

use Assert,
	Nella\Mocks\Media\File as FileMock;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Media/File.php';

class FileTest extends \TestCase
{
	/** @var \Nella\Media\Storages\File */
	private $storage;
	/** @var string */
	private $dir;

	public function setup()
	{
		$this->dir = TEMP_DIR;
		$this->storage = new \Nella\Media\Storages\File($this->dir);
	}

	/**
	 * @param string
	 */
	private function removeImage($path)
	{
		@unlink($this->dir . '/' . $path);
	}

	/**
	 * @param string
	 */
	private function copyImage($path)
	{
		@copy(FIXTURES_DIR . '/logo.png', $this->dir . '/' . $path);
	}

	public function testInstanceOf()
	{
		Assert::true($this->storage instanceof \Nella\Media\IStorage, 'is Nella\Media\IStorage');
	}

	public function dataTest()
	{
		$file = new FileMock;
		$file->path = 'tmp-logo-' . mt_rand(0, 9) . '.png';
		return array(array($file));
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testSave($file)
	{
		$source = FIXTURES_DIR . '/logo.png';

		$this->storage->save($file, $source);

		Assert::true(file_exists($this->dir . '/' . $file->path));

		$this->removeImage($file->path);
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testLoad($file)
	{
		$this->copyImage($file->path);
		$path = $this->storage->load($file);

		Assert::equal($this->dir . '/' . $file->path, $path);

		$this->removeImage($file->path);
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testRemove($file)
	{
		$this->copyImage($file->path);
		$this->storage->remove($file);

		Assert::false(file_exists($this->dir . '/' . $file->path));

		$this->removeImage($file->path);
	}

	public function testLoadInvalidFile()
	{
		$file = new FileMock;
		$file->path = 'tmp-logo-invalid.png';

		Assert::null($this->storage->load($file));
	}
}
