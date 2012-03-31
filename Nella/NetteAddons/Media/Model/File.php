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
 * File media type entity
 *
 * @author	Patrik Votoček
 */
class File extends \Nette\Object implements \Nella\NetteAddons\Media\IFile
{
	/** @var string */
	private $path;
	/** @var string */
	private $slug;
	/** @var string */
	private $contentType;

	/**
	 * @param string
	 */
	public function __construct($path)
	{
		$this->path = $path;
		$this->slug = pathinfo($path, PATHINFO_FILENAME);
		$this->parseContentType(pathinfo($path, PATHINFO_EXTENSION));
	}

	/**
	 * @param string
	 * @return string
	 */
	protected function parseContentType($ext)
	{
		$this->contentType = \Nella\NetteAddons\Media\Helper::extToMimeType($ext);
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @return \DateTime
	 */
	public function getUploaded()
	{
		return new \DateTime;
	}

	/**
	 * @return string
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function getFullSlug()
	{
		return $this->getSlug();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getFullSlug();
	}
}
