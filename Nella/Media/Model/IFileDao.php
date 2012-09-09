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

/**
 * File dao interface
 *
 * @author	Patrik Votoček
 */
interface IFileDao
{
	/**
	 * @param string
	 * @return \Nella\Media\IFile|NULL
	 */
	public function findOneByFullSlug($slug);
}

