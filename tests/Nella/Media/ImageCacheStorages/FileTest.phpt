<?php
/**
 * Test: Nella\Media\ImageCacheStorages\File
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Media\ImageCacheStorages;

use Tester\Assert,
	Nette\Caching\Cache,
	Nella\Media\ImageCacheStorages\File,
	Nella\Mocks\Media\Image,
	Nella\Mocks\Media\Format;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Media/Image.php';
require_once MOCKS_DIR . '/Media/Format.php';

class FileTest extends \Tester\TestCase
{
	/** @var \Nella\Media\ImageCacheStorages\File */
	private $storage;
	/** @var string */
	private $dir;
	/** @var \Nette\Caching\Storages\MemoryStorage */
	private $cache;

	public function setUp()
	{
		parent::setUp();
		$this->cache = new \Nette\Caching\Storages\MemoryStorage;
		$this->dir = TEMP_DIR;
		$this->storage = new File($this->dir, $this->cache);
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
	protected function prepareFile($path)
	{
		$path = $this->dir . '/' . $path;
		$dir = dirname($path);
		if (!file_exists($dir)) {
			@mkdir($dir, 0777, TRUE);
		}
		@copy(FIXTURES_DIR . '/logo.png', $path);
	}

	public function testInstanceOf()
	{
		Assert::true($this->storage instanceof \Nella\Media\IImageCacheStorage, 'is Nella\Media\IImageCacheStorage');
	}

	public function dataTest()
	{
		$image = new Image;
		$image->path = 'tmp-logo' . mt_rand(0, 9) . '.png';
		$image->slug = 'test-image' . mt_rand(0, 9);
		$format = new Format;
		$format->slug = 'test-format' . mt_rand(0, 9);
		return array(array($image, $format));
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testSave($image, $format)
	{
		$source = FIXTURES_DIR . '/logo.png';

		$this->storage->save($image, $format, "png", \Nette\Image::fromFile($source));

		Assert::true(file_exists($this->dir . '/' . $format->fullSlug . '/' . $image->fullSlug . '.png'));
		$this->removeImage($format->fullSlug . '/' . $image->fullSlug . '.png');
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testLoad($image, $format)
	{
		$this->prepareFile($format->fullSlug . '/' . $image->fullSlug . '.png');
		$path = $this->storage->load($image, $format, "png");

		Assert::equal($this->dir . '/' . $format->fullSlug . '/' . $image->fullSlug . '.png', $path);
		$this->removeImage($format->fullSlug . '/' . $image->fullSlug . '.png');
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testLoadInvalidType($image, $format)
	{
		$this->prepareFile($format->fullSlug . '/' . $image->fullSlug . '.png');
		Assert::null($this->storage->load($image, $format, "jpg"));
		$this->removeImage($format->fullSlug . '/' . $image->fullSlug . '.png');
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testLoadInvalidImage($image, $format)
	{
		$src = $format->fullSlug . '/' . $image->fullSlug . '.png';
		$this->prepareFile($src);

		$image->slug = 'invalid';

		Assert::null($this->storage->load($image, $format, "png"));
		$this->removeImage($src);
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testLoadInvalidFormat($image, $format)
	{
		$src = $format->fullSlug . '/' . $image->fullSlug . '.png';
		$this->prepareFile($src);

		$format->slug = 'invalid';

		Assert::null($this->storage->load($image, $format, "png"));
		$this->removeImage($src);
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testRemove($image, $format)
	{
		$src = $format->fullSlug . '/' . $image->fullSlug . '.png';
		$this->prepareFile($src);
		$cache = new Cache($this->cache, File::CACHE_NAME);
		$cache->save('image-test-image', array($src));

		$this->storage->remove($image);
		Assert::false(file_exists($src));
		$this->removeImage($src);
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testClean($image, $format)
	{
		$src = $format->fullSlug . '/' . $image->fullSlug . '.png';
		$this->prepareFile($src);

		$this->storage->clean($format);
		Assert::false(file_exists($src));
		$this->removeImage($src);
	}
}

id(new FileTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
