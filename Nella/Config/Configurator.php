<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Config;

/**
 * Initial system DI container generator.
 *
 * @author	Patrik Votoček
 * 
 * @property-read \Nella\SplClassLoader $splClassLoader
 */
class Configurator extends \Nette\Config\Configurator
{
	/**
	 * @return \Nella\SplClassLoader
	 */
	public function getSplClassLoader()
	{
		return \Nella\SplClassLoader::getInstance();
	}
}