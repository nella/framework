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

use Doctrine\ORM\Mapping as orm;

/**
 * File resource entity
 *
 * @orm\entity
 * @orm\table(name="media_files")
 *
 * @orm\inheritanceType("JOINED")
 * @orm\discriminatorColumn(name="type", type="string")
 * @orm\discriminatorMap({"base" = "FileEntity"})
 *
 * @author	Patrik Votoček
 */
class FileEntity extends BaseFileEntity
{
}

