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
 * File service
 *
 * @author	Patrik Votoček
 */
class FileService extends Service
{
	const STORAGE_DIR = "%storageDir%/files";
	
	/**
	 * @param array|\Traversable|\Nette\Http\FileUpload
	 * @param bool
	 * @return FileEntity
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nella\Models\Exception
	 * @throws \Nella\Models\EmptyValueException
	 * @throws \Nella\Models\DuplicateEntryException
	 */
	public function create($values, $withoutFlush = FALSE)
	{
		try {
			if (!$values instanceof \Nette\Http\FileUpload && isset($values['file']) && $values['file'] instanceof \Nette\Http\FileUpload) {
				return $this->createFromUpload($values['file']);
			}
			
			return parent::create($values, $withoutFlush);
		} catch (\PDOException $e) {
			$this->processPDOException($e);
		}
	}
}
