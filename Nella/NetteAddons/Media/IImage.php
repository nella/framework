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
 * Image media type interface
 *
 * @author	Patrik Votoček
 *
 * @property-read string $imageType
 */
interface IImage extends IFile
{
	/**
	 * @return string
	 */
	public function getImageType();
}