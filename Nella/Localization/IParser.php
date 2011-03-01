<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Localization;

/**
 * Localization parser interface
 *
 * @author	Patrik Votoček
 */
interface IParser
{
	/**
	 * @param mixed
	 * @param string save file path
	 */
	public function encode($var, $file);
	
	/**
	 * @param string file path
	 * @return array
	 */
	public function decode($file);
}