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
 * Base file media entity
 *
 * @mappedSuperclass
 *
 * @author	Patrik Votoček
 *
 * @property-read \DateTime $uploaded
 * @property string $path
 * @property-read \SplFileInfo $fileInfo
 * @property-read string $mimeType
 * @property-read mixed $content
 * @property-read string $size
 * @property-read string $filename
 */
abstract class BaseFileEntity extends \Nella\Doctrine\Entity implements IFile
{
	/**
	 * @column(type="datetimetz")
	 * @var \DateTime
	 */
	private $uploaded;
	/**
	 * @column(length=256)
	 * @var string
	 */
	private $path;

	public function __construct()
	{
		parent::__construct();
		$this->uploaded = new \DateTime;
	}

	/**
	 * @internal
	 * @return string
	 */
	protected function getStorageDir()
	{
		if ($this instanceof FileEntity) {
			return \Nette\Environment::getContext()->expand(FileService::STORAGE_DIR);
		} else {
			return \Nette\Environment::getContext()->expand(ImageService::STORAGE_DIR);
		}
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
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param string
	 * @return FileEntity
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * @return \SplFileInfo
	 */
	public function getFileinfo()
	{
		return new \SplFileInfo($this->getStorageDir() . "/" . $this->path);
	}

	/**
	 * @return string
	 */
	public function getMimeType()
	{
		return \Nette\Utils\MimeTypeDetector::fromFile($this->getStorageDir() . "/" . $this->path);
	}

	/**
	 * @return mixed
	 */
	public function getContent()
	{
		return file_get_contents($this->getStorageDir() . "/" . $this->path, FILE_BINARY);
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return filesize($this->getStorageDir() . "/" . $this->path);
	}

	/**
	 * @return string
	 */
	public function getFilename()
	{
		return pathinfo($this->getStorageDir() . "/" . $this->path, PATHINFO_BASENAME);
	}

	/**
	 * @param string
	 */
	public function write($path)
	{
		return copy($this->getStorageDir() . "/" . $this->path, $path);
	}
}
