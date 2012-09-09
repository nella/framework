<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media\Doctrine;

use Doctrine\ORM\Mapping as orm;

/**
 * File facade
 *
 * @author	Patrik Votoček
 */
class FileFacade extends BaseFacade implements \Nella\Media\Model\IFileDao
{
	/**
	 * @param string
	 * @return \Nella\Media\Doctrine\FileEntity|NULL
	 */
	public function findOneByFullSlug($fullSlug)
	{
		return parent::findOneByFullSlug($fullSlug);
	}
}

