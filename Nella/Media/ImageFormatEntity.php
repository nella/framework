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
 * Resource image format entity
 *
 * @entity(repositoryClass="Nella\Media\MediaRepository")
 * @table(name="media_formats")
 * @hasLifecycleCallbacks
 *
 * @author	Patrik Votoček
 *
 * @property int $width
 * @property int $height
 * @property bool $crop
 * @property ImageEntity $watermark
 * @property int $watermarkOpacity
 * @property int $watermarkPosition
 * @property string $slug
 */
class ImageFormatEntity extends \Nella\Doctrine\Entity implements IImageFormat
{
	/**
	 * @column(type="integer")
	 * @var int
	 */
	private $width;
	/**
	 * @column(type="integer")
	 * @var int
	 */
	private $height;
	/**
	 * @column(type="boolean")
	 * @var bool
	 */
	private $crop;
	/**
	 * @manyToOne(targetEntity="Nella\Media\ImageEntity")
     * @joinColumn(name="watermark_id", referencedColumnName="id")
	 * @var ImageEntity
	 */
	private $watermark;
	/**
	 * @column(type="integer", nullable=true)
	 * @var int
	 */
	private $watermarkOpacity;
	/**
	 * @column(type="integer", nullable=true)
	 * @var int
	 */
	private $watermarkPosition;
	/**
	 * @column(unique=true, nullable=true)
	 * @var string
	 */
	private $slug;

	public function __construct()
	{
		parent::__construct();
		$this->crop = TRUE;
		$this->watermark = NULL;
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
		$this->watermark;
	}

	/**
	 * @param ImageEntity
	 * @return ImageFormatEntity
	 */
	public function setWatermark(IImage $watermark = NULL)
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
	 * @param string
	 * @return ImageFormatEntity
	 */
	public function setSlug($slug)
	{
		$this->slug = $this->sanitizeString($slug);
		return $this;
	}

	/**
	 * @param IImage
	 * @return \Nette\Image
	 */
	public function process(IImage $image)
	{
		$image = $image->toImage();
		if ($this->crop) {
			$image->resize($this->width, $this->height, \Nette\Image::FILL | \Nette\Image::ENLARGE)
				->crop('50%', '50%', $this->width, $this->height);
		} else {
			$image->resize($this->width, $this->height);
		}

		if ($this->watermark) {
			throw new \Nette\NotImplementedException;
		}

		return $image;
	}
}
