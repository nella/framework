<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Media\Callbacks;

use Nella\Media\IStorage,
	Nella\Media\IFile,
	Nette\Application\Responses\FileResponse;

/**
 * File presenter callback (convert request to response)
 *
 * @author	Patrik Votoček
 */
class FilePresenterCallback extends \Nette\Object implements \Nella\Media\IFilePresenterCallback
{
	/** @var \Nella\Media\IStorage */
	private $storage;

	/**
	 * @param \Nella\Media\IStorage
	 */
	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * @param \Nella\Media\IFile
	 * @return \Nette\Application\Responses\FileResponse
	 */
	public function __invoke(IFile $file)
	{
		$path = $this->storage->load($file);
		if (!$path) {
			throw new \Nette\Application\BadRequestException('File not found', 404);
		}

		return new FileResponse($path, pathinfo($path, PATHINFO_BASENAME), $file->getContentType());
	}
}

