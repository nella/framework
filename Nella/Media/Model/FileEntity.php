<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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
