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
 * File media type interface
 *
 * @author	Patrik Votoček
 */
interface IFile
{
	/**
	 * @return \DateTime
	 */
	public function getUploaded();

	/**
	 * @return string
	 */
	public function getMimeType();

	/**
	 * @return mixed
	 */
	public function getContent();

	/**
	 * @return int
	 */
	public function getSize();

	/**
	 * @return string
	 */
	public function getFilename();

	/**
	 * @param string
	 */
	public function write($path);
}
