<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Media;

/**
 * Media presenter
 *
 * @author	Patrik Votoček
 */
class MediaPresenter extends \Nella\Application\UI\MicroPresenter
{
	/**
	 * @param \Nette\Application\Request
	 * @return \Nette\Application\IResponse
	 */
	public function run(\Nette\Application\Request $request)
	{
		$params = $request->params;
		if (isset($params['file'])) {
			return callback($this, 'actionFile')->invokeArgs(array('file' => $params['file']));
		} elseif (isset($params['image']) && isset($params['format'])) {
			return callback($this, 'actionImage')->invokeArgs(array(
				'image' => $params['image'],
				'format' => $params['format'],
				'path' => $params['path'],
				'type' => isset($params['type']) ? $params['type'] : NULL
			));
		} else {
			return parent::run($request);
		}
	}

	/**
	 * @param IFile
	 */
	public function actionFile(IFile $file)
	{
		return new \Nette\Application\Responses\FileResponse(
			$file->getFileinfo()->getRealPath(), //$file->getContent(),
			$file->getFilename(),
			$file->getMimeType()
		);
	}
	
	/**
	 * @param IImage
	 * @param IImageFormat
	 * @return \Nette\Image
	 */
	protected function processImage(IImage $image, IImageFormat $format)
	{
		$image = $image->toImage();
		
		if ($format->crop) {
			$image->resize($format->width, $format->height, \Nette\Image::FILL | \Nette\Image::ENLARGE)
				->crop('50%', '50%', $format->width, $format->height);
		} else {
			$image->resize($format->width, $format->height);
		}

		if ($format->watermark) {
			$watermark = $format->watermark->toImage();
			$image = new \Nette\Image;
			switch ($format->watermarkPosition) {
				case IImageFormat::POSITION_BOTTOM_LEFT:
					$left = 0;
					$top = $image->height - $watermark->height;
					break;
				case IImageFormat::POSITION_BOTTOM_RIGHT:
					$left = $image->width - $watermark->width;
					$top = $image->height - $watermark->height;
					break;
				case IImageFormat::POSITION_CENTER;
					$top = ($image->height / 2) - ($watermark->height / 2);
					$left = ($image->width / 2) - ($watermark->width / 2);
					break;
				case IImageFormat::POSITION_TOP_RIGHT:
					$top = 0;
					$left = $image->width - $watermark->width;
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
			
			$image->place($watermark, $left, $top, $format->watermarkOpacity);
		}
		
		return $image;
	}

	/**
	 * @param IImage
	 * @param IFormat
	 * @param string
	 * @param int
	 */
	public function actionImage($image, $format, $path, $type = NULL)
	{
		$service = $this->getContext()->doctrineContainer->getService('Nella\Media\ImageEntity');
		$image = $service->repository->find($image);
		
		$service = $this->getContext()->doctrineContainer->getService('Nella\Media\ImageFormatEntity');
		$format = $service->repository->find($format);
			
		$image = $this->processImage($image, $format);
		$context = $this->getContext();

		$path = $context->expand($context->params['wwwDir']) . $path;
		$dir = pathinfo($path, PATHINFO_DIRNAME);
		if (!file_exists($dir)) {
			mkdir($dir, 0777, TRUE);
		}

		$image->save($path);
		if (!$type) {
			$image->send();
		} else {
			switch ($type) {
				case 'gif':
					$type = \Nette\Image::GIF;
					break;
				case 'png':
					$type = \Nette\Image::PNG;
					break;
				default:
					$type = \Nette\Image::JPEG;
					break;
			}
			$image->send($type);
		}

		$this->terminate();
	}
}
