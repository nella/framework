<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
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
	/** @var string */
	public static $fullSlugFormat = '<id>-<slug>';
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
		return str_replace(array('<id>', '<slug>'), array($this->getId(), $this->getSlug()), static::$fullSlugFormat);
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
