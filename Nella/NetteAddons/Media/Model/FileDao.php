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
 * File dao
 *
 * @author	Patrik Votoček
 */
class FileDao extends \Nette\Object implements IFileDao
{
	/**
	 * @param string
	 * @return \Nella\NetteAddons\Media\File|NULL
	 */
	public function findOneByFullSlug($slug)
	{
		if (($pos = strrpos($slug, '_')) === FALSE) {
			return NULL;
		}

		$path = substr_replace($slug, '.', $pos, 1);

		try {
			return new File($path);
		} catch (\Nette\InvalidArgumentException $e) {
			return NULL;
		}
	}
}
