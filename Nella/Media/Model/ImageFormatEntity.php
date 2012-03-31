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
 * Resource image format entity
 *
 * @orm\entity
 * @orm\table(name="media_formats")
 *
 * @author	Patrik Votoček
 *
 * @property int $width
 * @property int $height
 * @property int $flags
 * @property bool $crop
 * @property ImageEntity $watermark
 * @property int $watermarkOpacity
 * @property int $watermarkPosition
 * @property string $slug
 * @property bool $authenticated
 */
class ImageFormatEntity extends \Nella\Doctrine\Entity implements \Nella\NetteAddons\Media\IImageFormat
{
	/** @var string */
	public static $fullSlugFormat = '<id>-<slug>';
	/** @var array */
	public $onFlush = array();
	/**
	 * @orm\column(type="integer")
	 * @var int
	 */
	private $width;
	/**
	 * @orm\column(type="integer")
	 * @var int
	 */
	private $height;
	/**
	 * @orm\column(type="integer")
	 * @var int
	 */
	private $flags;
	/**
	 * @orm\column(type="boolean")
	 * @var bool
	 */
	private $crop;
	/**
	 * @orm\manyToOne(targetEntity="ImageEntity")
     * @orm\joinColumn(name="watermark_id", referencedColumnName="id")
	 * @var ImageEntity
	 */
	private $watermark;
	/**
	 * @orm\column(type="integer", nullable=true)
	 * @var int
	 */
	private $watermarkOpacity;
	/**
	 * @orm\column(type="integer", nullable=true)
	 * @var int
	 */
	private $watermarkPosition;
	/**
	 * @orm\column(unique=true, nullable=true)
	 * @var string
	 */
	private $slug;

	public function __construct()
	{
		parent::__construct();
		$this->flags = 0;
		$this->crop = FALSE;
		$this->watermark = NULL;
		$this->watermarkOpacity = NULL;
		$this->watermarkPosition = NULL;
	}

	/**
	 * @return int	pixels
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @param int
	 * @return ImageFormatEntity
	 */
	public function setWidth($width)
	{
		$this->width = $width;
		return $this;
	}

	/**
	 * @return int	pixels
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @param int
	 * @return ImageFormatEntity
	 */
	public function setHeight($height)
	{
		$this->height = $height;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getFlags()
	{
		return $this->flags;
	}

	/**
	 * @param int
	 * @return ImageFormatEntity
	 */
	public function setFlags($flags)
	{
		$this->flags = $flags;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isCrop()
	{
		return $this->crop;
	}

	/**
	 * @param bool
	 * @return ImageFormatEntity
	 */
	public function setCrop($crop)
	{
		$this->crop = $crop;
		return $this;
	}

	/**
	 * @return ImageEntity
	 */
	public function getWatermark()
	{
		return $this->watermark;
	}

	/**
	 * @param ImageEntity
	 * @return ImageFormatEntity
	 */
	public function setWatermark(\Nella\NetteAddons\Media\IImage $watermark = NULL)
	{
		$this->watermark = $watermark;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getWatermarkOpacity()
	{
		return $this->watermarkOpacity;
	}

	/**
	 * @param int
	 * @return ImageFormatEntity
	 */
	public function setWatermarkOpacity($opacity)
	{
		$this->watermarkOpacity = $opacity;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getWatermarkPosition()
	{
		return $this->watermarkPosition;
	}

	/**
	 * @param int
	 * @return ImageFormatEntity
	 */
	public function setWatermarkPosition($position)
	{
		$this->watermarkPosition = $position;
		return $this;
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
	 * @return ImageFormatEntity
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
