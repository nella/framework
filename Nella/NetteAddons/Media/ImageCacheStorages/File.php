<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Media\ImageCacheStorages;

use Nette\Caching\Cache,
	Nella\NetteAddons\Media\IImage,
	Nella\NetteAddons\Media\IImageFormat;

/**
 * File image cache storage
 *
 * @author	Patrik Votoček
 */
class File extends \Nette\Object implements \Nella\NetteAddons\Media\IImageCacheStorage
{
	const CACHE_NAME = 'Nella.Media.Cache';

	/** @var \Nette\Caching\Cache */
	private $cache;
	/** @var string */
	private $dir;

	/**
	 * @param string
	 * @param \Nette\Caching\IStorage
	 */
	public function __construct($dir, \Nette\Caching\IStorage $cacheStorage = NULL)
	{
		if (!file_exists($dir)) {
			if (!@mkdir($dir, 0777, TRUE)) {
				throw new \Nette\InvalidStateException();
			}
		} elseif (!is_writable($dir)) {
			throw new \Nette\InvalidStateException();
		}
		$this->dir = $dir;

		if (!$cacheStorage) {
			$cacheStorage = new \Nette\Caching\Storages\MemoryStorage;
		}
		$this->cache = new Cache($cacheStorage, static::CACHE_NAME);
	}

	/**
	 * @param IImage
	 * @param IImageFormat
	 * @param string
	 * @return \Nette\Image|string
	 */
	public function load(IImage $image, IImageFormat $format, $type)
	{
		$path = $this->formatPath($image, $format, $type);
		if (file_exists($path)) {
			return $path;
		}
		return NULL;
	}

	/**
	 * @param IImage
	 * @param IImageFormat
	 * @param string
	 * @param \Nette\Image|string
	 */
	public function save(IImage $image, IImageFormat $format, $type, $from)
	{
		$path = $this->formatPath($image, $format, $type);
		$dir = dirname($path);
		if (!file_exists($dir)) {
			@mkdir($dir, 0777, TRUE);
		}
		if ($from instanceof \Nette\Image) {
			$from->save($path);
		} else {
			@copy($from, $path);
		}

		// image files list
		$files = $this->getCacheImageFiles($image);
		$files[] = $path;
		$this->cache->save($this->getCacheImageFilesKey($image), $files);
	}

	/**
	 * @param IImage
	 */
	public function remove(IImage $image)
	{
		foreach ($this->getCacheImageFiles($image) as $path) {
			if (file_exists($path)) {
				@unlink($path);
			}
		}
	}

	/**
	 * @param IImageFormat|NULL
	 */
	public function clean(IImageFormat $format = NULL)
	{
		if ($format) {
			$this->cleanDir($this->formatFormatPath($format), TRUE);
		} else {
			$this->cleanDir($this->dir);
		}
	}

	/**
	 * @param IImageFormat
	 * @return string
	 */
	protected function formatFormatPath(IImageFormat $format)
	{
		return $this->dir . "/" . $format->getFullSlug();
	}

	/**
	 * @param IImage
	 * @param IImageFormat
	 * @param string
	 * @return string
	 */
	protected function formatPath(IImage $image, IImageFormat $format, $type)
	{
		return $this->formatFormatPath($format) . "/" . $image->getFullSlug() . "." . $type;
	}

	/**
	 * @param IImage
	 * @return string
	 */
	protected function getCacheImageFilesKey(IImage $image)
	{
		return 'image-'.$image->getFullSlug();
	}

	/**
	 * @param IImage
	 * @return array
	 */
	protected function getCacheImageFiles(IImage $image)
	{
		return $this->cache->load($this->getCacheImageFilesKey($image)) ?: array();
	}

	/**
	 * @param string
	 * @param bool
	 */
	protected function cleanDir($dir, $with = FALSE)
	{
		if (!file_exists($dir)) {
			return;
		}

		foreach (\Nette\Utils\Finder::find('*')->in($dir)->childFirst() as $item) {
			if ($item->isFile()) {
				@unlink($item->getRealPath());
			} elseif ($item->isDir()) {
				@rmdir($item->getRealPath());
			}
		}

		if ($with) {
			@rmdir($dir);
		}
	}
}
