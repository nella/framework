<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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