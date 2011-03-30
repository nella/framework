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
abstract class BaseFileEntity extends \Nella\Models\Entity implements IFile
{
	/**
	 * @column(type="datetime")
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
		return new \SplFileInfo(STORAGE_DIR . "/{$this->path}");
	}
	
	/**
	 * @return string
	 */
	public function getMimeType()
	{
		return \Nette\Tools::detectMimeType(STORAGE_DIR . "/{$this->path}");
	}
	
	/**
	 * @return mixed
	 */
	public function getContent()
	{
		return file_get_contents(STORAGE_DIR . "/{$this->path}", FILE_BINARY);
	}
	
	/**
	 * @return int
	 */
	public function getSize()
	{
		return filesize(STORAGE_DIR . "/{$this->path}");
	}
	
	/**
	 * @return string
	 */
	public function getFilename()
	{
		return pathinfo(STORAGE_DIR . "/{$this->path}", PATHINFO_BASENAME);
	}
	
	/**
	 * @param string
	 */
	public function write($path)
	{
		return copy(STORAGE_DIR . "/{$this->path}", $path);
	}
}
