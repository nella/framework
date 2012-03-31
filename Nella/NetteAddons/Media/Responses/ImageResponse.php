<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Media\Responses;

/**
 * Image response
 *
 * @author	Patrik Votoček
 * @author	David Grudl
 */
class ImageResponse extends \Nette\Object implements \Nette\Application\IResponse
{
	/** @var \Nette\Image|string */
	private $image;
	/** @var bool */
	public $resuming = TRUE;

	/**
	 * @param \Nette\Image|string
	 */
	public function __construct($image)
	{
		if (!$image instanceof \Nette\Image && !file_exists($image)) {
			throw new \Nette\InvalidArgumentException('Image must be Nette\Image or file path');
		}

		$this->image = $image;
	}

	/**
	 * @param \Nette\Http\IRequest
	 * @param \Nette\Http\IResponse
	 */
	public function send(\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse)
	{
		if ($this->image instanceof \Nette\Image) {
			$this->image->send();
			return;
		}

		$httpResponse->setContentType(\Nette\Utils\MimeTypeDetector::fromFile($this->image));

		$filesize = $length = filesize($this->image);
		$handle = fopen($this->image, 'r');

		 if ($this->resuming) {
		 	$httpResponse->setHeader('Accept-Ranges', 'bytes');
		 	$range = $httpRequest->getHeader('Range');
		 	if ($range !== NULL) {
		 		$range = substr($range, 6); // 6 == strlen('bytes=')
		 		list($start, $end) = explode('-', $range);
		 		if ($start == NULL) {
		 			$start = 0;
		 		}
		 		if ($end == NULL) {
		 			$end = $filesize - 1;
		 		}

		 		if ($start < 0 || $end <= $start || $end > $filesize -1) {
		 			$httpResponse->setCode(416); // requested range not satisfiable
		 			return;
		 		}

		 		$httpResponse->setCode(206);
		 		$httpResponse->setHeader('Content-Range', 'bytes ' . $start . '-' . $end . '/' . $filesize);
		 		$length = $end - $start + 1;
		 		fseek($handle, $start);
		 	} else {
		 		$httpResponse->setHeader('Content-Range', 'bytes 0-' . ($filesize - 1) . '/' . $filesize);
		 	}
		 }

		 $httpResponse->setHeader('Content-Length', $length);
		 while (!feof($handle)) {
		 	echo fread($handle, 4e6);
		 }
		 fclose($handle);
	}
}
