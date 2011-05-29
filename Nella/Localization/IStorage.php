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
 * Localization storage interface
 *
 * @author	Patrik Votoček
 */
interface IStorage
{
	/**
	 * @param Dictionary
	 * @param string
	 */
	public function save(Dictionary $dictionary, $lang);
	
	/**
	 * @param string
	 * @return Dictionary
	 */
	public function load($lang, Dictionary $dictionary);
}