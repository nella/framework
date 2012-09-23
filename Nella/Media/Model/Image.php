<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media\Model;

use Nette\Utils\Strings,
	Nella\Media\Helper;

/**
 * Image media type entity
 *
 * @author	Patrik Votoček
 */
class Image extends File implements \Nella\Media\IImage
{
	/**
	 * @param string
	 */
	public function __construct($path)
	{
		parent::__construct($path);
		if (!Strings::startsWith($this->getContentType(), 'image/')) {
			throw new \Nette\InvalidArgumentException('Only image file types supported');
		}
	}

	/**
	 * @return string
	 */
	public function getImageType()
	{
		return Helper::mimeTypeToExt($this->getContentType());
	}
}

