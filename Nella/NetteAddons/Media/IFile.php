<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\NetteAddons\Media;

/**
 * File media type interface
 *
 * @author	Patrik Votoček
 *
 * @property-read string $path
 * @property-read \DateTime $uploaded
 * @property-read string $contentType
 * @property string $slug
 */
interface IFile
{
	/**
	 * @return string
	 */
	public function getPath();

	/**
	 * @return \DateTime
	 */
	public function getUploaded();

	/**
	 * @return string
	 */
	public function getContentType();

	/**
	 * @return string
	 */
	public function getSlug();

	/**
	 * @return string
	 */
	public function getFullSlug();
}
