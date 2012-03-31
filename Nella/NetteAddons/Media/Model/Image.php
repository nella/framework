<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\NetteAddons\Media\Model;

/**
 * Image media type entity
 *
 * @author	Patrik Votoček
 */
class Image extends File implements \Nella\NetteAddons\Media\IImage
{
	/**
	 * @param string
	 */
	public function __construct($path)
	{
		parent::__construct($path);
		if (!\Nette\Utils\Strings::startsWith($this->getContentType(), 'image/')) {
			throw new \Nette\InvalidArgumentException('Only image file types supported');
		}
	}

	/**
	 * @return string
	 */
	public function getImageType()
	{
		return \Nella\NetteAddons\Media\Helper::mimeTypeToExt($this->getContentType());
	}
}
