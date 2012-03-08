<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\NetteAddons\Media\Model;

/**
 * File dao interface
 *
 * @author	Patrik Votoček
 */
interface IImageFormatDao
{
	/**
	 * @param string
	 * @return \Nella\NetteAddons\Media\IImageFormat|NULL
	 */
	public function findOneByFullSlug($slug);
}
