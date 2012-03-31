<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Media;

/**
 * Image presenter callback response interface
 *
 * @author	Patrik Votoček
 */
interface IImagePresenterCallback
{
	/**
	 * @param IImage
	 * @param IImageFormat
	 * @param string
	 * @return \Nette\Application\IResponse
	 */
	public function __invoke(IImage $image, IImageFormat $format, $type);
}