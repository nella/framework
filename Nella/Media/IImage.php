<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Media;

/**
 * Image media type interface
 *
 * @author	Patrik Votoček
 */
interface IImage extends IFile
{
	/**
	 * @return \Nella\Imgage
	 */
	public function toImage();
}