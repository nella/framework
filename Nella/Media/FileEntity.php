<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Media;

/**
 * File media entity
 *
 * @entity
 * @table(name="media_files")
 * @service(class="Nella\Media\FileService")
 *
 * @inheritanceType("JOINED")
 * @discriminatorColumn(name="type", type="string")
 * @discriminatorMap({"base" = "FileEntity"})
 *
 * @author	Patrik Votoček
 */
class FileEntity extends BaseFileEntity
{

}
