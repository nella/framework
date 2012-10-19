<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Mocks\Media;

class File extends \Nette\Object implements \Nella\Media\IFile
{
	public $path = 'tmp-logo.png';

	public function getPath()
	{
		return $this->path;
	}

	public function getUploaded()
	{

	}

	public function getContentType()
	{
		return 'image/png';
	}

	public function getSlug()
	{

	}

	public function getFullSlug()
	{

	}

}
