<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Media;

/**
 * File media storage interface
 *
 * @author	Patrik Votoček
 */
interface IStorage
{
	/**
	 * @param IFile
	 * @return string full path
	 */
	public function load(IFile $file);

	/**
	 * @param IFile
	 * @param string temp file full path
	 */
	public function save(IFile $file, $from);

	/**
	 * @param IFile
	 */
	public function remove(IFile $file);
}
