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
 * Image media entity
 *
 * @entity(repositoryClass="Nella\Media\MediaRepository")
 * @table(name="media_images")
 * @service(class="Nella\Media\ImageService")
 * @hasLifecycleCallbacks
 *
 * @inheritanceType("JOINED")
 * @discriminatorColumn(name="type", type="string")
 * @discriminatorMap({"base" = "ImageEntity"})
 *
 * @author	Patrik Votoček
 */
class ImageEntity extends BaseFileEntity implements IImage
{
	/**
	 * @column(unique=true, nullable=true)
	 * @var sting
	 */
	private $slug;
	
	/**
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param string
	 * @return ImageEntity
	 */
	public function setSlug($slug)
	{
		$this->slug = $this->sanitizeString($slug);
		return $this;
	}
	
	/**
	 * @return \Nella\Image
	 */
	public function toImage()
	{
		return \Nella\Image::fromFile($this->getStorageDir() . "/" . $this->getPath());
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		$types = array(
			'image/jpg' => "jpg",
			'image/jpeg' => "jpg",
			'image/png' => "png",
			'image/gif' => "gif",
		);

		$mime = $this->getMimeType();
		return isset($types[$mime]) ? $types[$mime] : "jpg";
	}
	
	/**
	 * @internal
	 * @onFlush
	 */
	public function onFlush()
	{
		if (!$this->slug) {
			$this->slug = $this->id;
		}
	}
}
