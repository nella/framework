<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media\Callbacks;

use Nette\Image,
	Nella\Media\IImage,
	Nella\Media\IImageFormat;

/**
 * Image presenter callback (convert request to response)
 *
 * @author	Patrik Votoček
 */
class ImagePresenterCallback extends \Nette\Object implements \Nella\Media\IImagePresenterCallback
{
	/** @var \Nella\Media\IStorage */
	private $storage;
	/** @var \Nella\Media\IImageCacheStorage */
	private $cacheStorage;

	/**
	 * @param \Nella\Media\IStorage
	 * @param \Nella\Media\IImageCacheStorage
	 */
	public function __construct(\Nella\Media\IStorage $storage, \Nella\Media\IImageCacheStorage $cacheStorage)
	{
		$this->storage = $storage;
		$this->cacheStorage = $cacheStorage;
	}

	/**
	 * @param \Nella\Media\IImage
	 * @param \Nella\Media\IImageFormat
	 * @param string
	 * @return \Nette\Application\Responses\FileResponse
	 */
	public function __invoke(IImage $image, IImageFormat $format, $type)
	{
		$path = $this->storage->load($image);
		if (!$path) {
			throw new \Nette\Application\BadRequestException('Image not found', 404);
		}

		$img = $this->load($image, $format, $type);
		if (!$img) {
			throw new \Nette\Application\BadRequestException('Image not found', 404);
		}
		return new \Nella\Media\Responses\ImageResponse($img);
	}

	/**
	 * @param string
	 * @return \Nette\Image
	 */
	protected function getImage($path)
	{
		return Image::fromFile($path);
	}

	/**
	 * @param IImage
	 * @param IImageFormat
	 * @param string
	 */
	protected function load(IImage $image, IImageFormat $format, $type)
	{
		$img = $this->cacheStorage->load($image, $format, $type);
		if (!$img) {
			$this->cacheStorage->save($image, $format, $type, $this->process($image, $format));
			$img = $this->cacheStorage->load($image, $format, $type);
		}

		return $img;
	}

	/**
	 * @param IImage
	 * @param IImageFormat
	 * @return \Nette\Image
	 */
	final protected function process(IImage $image, IImageFormat $format)
	{
		if ($format->getWidth() == 0 && $format->getHeight() == 0 && $format->getWatermark() == NULL) {
			return $this->storage->load($image);
		}
		$img = $this->getImage($this->storage->load($image));
		$img->resize($format->getWidth(), $format->getHeight(), $format->getFlags());
		if ($format->isCrop()) {
			$img->crop('50%', '50%', $format->getWidth(), $format->getHeight());
		}

		if ($format->getWatermark() && $wmimg = $this->storage->load($format->getWatermark())) {
			$watermark = $this->getImage($wmimg);

			switch ($format->getWatermarkPosition()) {
				case IImageFormat::POSITION_BOTTOM_LEFT:
					$left = 0;
					$top = $img->height - $watermark->height;
					break;
				case IImageFormat::POSITION_BOTTOM_RIGHT:
					$left = $img->width - $watermark->width;
					$top = $img->height - $watermark->height;
					break;
				case IImageFormat::POSITION_CENTER;
					$top = ($img->height / 2) - ($watermark->height / 2);
					$left = ($img->width / 2) - ($watermark->width / 2);
					break;
				case IImageFormat::POSITION_TOP_RIGHT:
					$top = 0;
					$left = $img->width - $watermark->width;
					break;
				case IImageFormat::POSITION_TOP_LEFT:
				default:
					$left = $top = 0;
					break;
			}
			if ($left < 0) {
				$left = 0;
			}
			if ($top < 0) {
				$top = 0;
			}

			$img->place($watermark, $left, $top, $format->getWatermarkOpacity());
		}

		return $img;
	}

	/**
	 * @param string
	 * @return string
	 */
	final protected function typeToContentType($type)
	{
		switch($type) {
			case 'gif':
				return 'image/gif';
				break;
			case 'png':
				return 'image/png';
				break;
			default:
				return 'image/jpeg';
				break;
		}
	}
}

