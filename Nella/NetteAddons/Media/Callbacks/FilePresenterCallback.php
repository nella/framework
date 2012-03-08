<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\NetteAddons\Media\Callbacks;

/**
 * File presenter callback (convert request to response)
 *
 * @author	Patrik Votoček
 */
class FilePresenterCallback extends \Nette\Object implements \Nella\NetteAddons\Media\IFilePresenterCallback
{
	/** @var \Nella\NetteAddons\Media\IStorage */
	private $storage;
	
	/**
	 * @param \Nella\NetteAddons\Media\IStorage
	 */
	public function __construct(\Nella\NetteAddons\Media\IStorage $storage)
	{
		$this->storage = $storage;
	}
	
	/**
	 * @param \Nella\NetteAddons\Media\IFile
	 * @return \Nette\Application\Responses\FileResponse
	 */
	public function __invoke(\Nella\NetteAddons\Media\IFile $file)
	{
		$path = $this->storage->load($file);
		if (!$path) {
			throw new \Nette\Application\BadRequestException('File not found', 404);
		}
		
		return new \Nette\Application\Responses\FileResponse($path, pathinfo($path, PATHINFO_BASENAME), $file->getContentType());
	}
}