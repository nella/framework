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
 * Base file resource entity
 *
 * @orm\mappedSuperclass
 *
 * @author	Patrik Votoček
 *
 * @property-read string $path
 * @property-read \DateTime $uploaded
 * @property-read string $contentType
 * @property string $slug
 * @property-read string $fullSlug
 */
abstract class BaseFileEntity extends \Nella\Doctrine\Entity implements \Nella\NetteAddons\Media\IFile
{
	/** @var array */
	public $onFlush = array();
	/**
	 * @orm\column
	 * @var string
	 */
	private $path;
	/**
	 * @orm\column(type="datetimetz")
	 * @var \DateTime
	 */
	private $uploaded;
	/**
	 * @orm\column
	 * @var string
	 */
	private $contentType;
	/**
	 * @orm\column
	 * @var string
	 */
	private $slug;

	public function __construct($path, $contentType)
	{
		parent::__construct();
		$this->uploaded = new \DateTime;
		$this->path = static::normalizeString($path);
		$this->contentType = static::normalizeString($contentType);
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
		return $this->uploaded;
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
		return "{$this->getId()}-{$this->getSlug()}";
	}
	
	/**
	 * @param string
	 * @return BaseFileEntity
	 */
	public function setSlug($slug)
	{
		$this->slug = static::normalizeString($slug);
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getFullSlug();
	}
}
