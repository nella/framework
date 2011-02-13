<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Models;

/**
 * Versionable entity interface
 *
 * @author	Patrik Votoček
 */
interface IVersionable
{
	/**
	 * @return int
	 */
	public function getId();
	
	/**
	 * @return string
	 */
	public function takeSnapshot();
}