<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\NetteAddons\Media\ImageCacheStorages;

use Nella\NetteAddons\Media\ImageCacheStorages\File,
	Nette\Caching\Cache;

class FileTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\NetteAddons\Media\ImageCacheStorages\File */
	private $storage;
	/** @var string */
	private $dir;
	/** @var \Nette\Caching\Storages\MemoryStorage */
	private $cache;

	public function setup()
	{
		$this->cache = new \Nette\Caching\Storages\MemoryStorage;
		$this->dir = $this->getContext()->parameters['tempDir'];
		$this->storage = new File($this->dir, $this->cache);
	}

	/**
	 * @param string
	 */
	protected function prepareFile($path)
	{
		$dir = dirname($path);
		if (!file_exists($dir)) {
			@mkdir($dir, 0777, TRUE);
		}
		@copy($this->getContext()->parameters['fixturesDir'] . '/logo.png', $path);
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\IImageCacheStorage', $this->storage, 'is Nella\NetteAddons\Media\IImageCacheStorage');
	}

	public function dataTest()
	{
		$image = $this->getMock('Nella\NetteAddons\Media\IImage');
		$image->expects($this->any())->method('getPath')->will($this->returnValue('tmp-logo.png'));
		$image->expects($this->any())->method('getFullSlug')->will($this->returnValue('test-image'));
		$format = $this->getMock('Nella\NetteAddons\Media\IImageFormat');
		$format->expects($this->any())->method('getFullSlug')->will($this->returnValue('test-format'));
		return array(array($image, $format));
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testSave($image, $format)
	{
		$source = $this->getContext()->parameters['fixturesDir'] . '/logo.png';

		$this->storage->save($image, $format, "png", \Nette\Image::fromFile($source));

		$this->assertFileExists($this->dir . '/test-format/test-image.png');
	}

	/**
	 * @depends testSave
	 * @dataProvider dataTest
	 */
	public function testLoad($image, $format)
	{
		$path = $this->storage->load($image, $format, "png");

		$this->assertEquals($this->dir . '/test-format/test-image.png', $path);
	}

	/**
	 * @depends testSave
	 * @dataProvider dataTest
	 */
	public function testLoadInvalidType($image, $format)
	{
		$this->assertNull($this->storage->load($image, $format, "jpg"));
	}

	/**
	 * @depends testSave
	 */
	public function testLoadInvalidImage()
	{
		$image = $this->getMock('Nella\NetteAddons\Media\IImage');
		$image->expects($this->any())->method('getPath')->will($this->returnValue('tmp-logo.png'));
		$image->expects($this->any())->method('getFullSlug')->will($this->returnValue('test-image-invalid'));
		$format = $this->getMock('Nella\NetteAddons\Media\IImageFormat');
		$format->expects($this->any())->method('getFullSlug')->will($this->returnValue('test-format'));

		$this->assertNull($this->storage->load($image, $format, "png"));
	}

	/**
	 * @depends testSave
	 */
	public function testLoadInvalidFormat()
	{
		$image = $this->getMock('Nella\NetteAddons\Media\IImage');
		$image->expects($this->any())->method('getPath')->will($this->returnValue('tmp-logo.png'));
		$image->expects($this->any())->method('getFullSlug')->will($this->returnValue('test-image'));
		$format = $this->getMock('Nella\NetteAddons\Media\IImageFormat');
		$format->expects($this->any())->method('getFullSlug')->will($this->returnValue('test-format-invalid'));

		$this->assertNull($this->storage->load($image, $format, "png"));
	}

	/**
	 * @depends testLoad
	 * @dataProvider dataTest
	 */
	public function testRemove($image, $format)
	{
		$path = $this->dir . '/test-format/test-image.png';
		$this->prepareFile($path);
		$cache = new Cache($this->cache, File::CACHE_NAME);
		$cache->save('image-test-image', array($path));

		$this->storage->remove($image);
		$this->assertFileNotExists($path);
	}

	/**
	 * @depends testLoad
	 * @dataProvider dataTest
	 */
	public function testClean($image, $format)
	{
		$path = $this->dir . '/test-format/test-image.png';
		$this->prepareFile($path);

		$this->storage->clean($format);
		$this->assertFileNotExists($path);
	}
}
