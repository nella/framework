<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella;

use Nette\Debug;

/**
 * Nette\Environment helper.
 *
 * @author	Patrik Votoček
 */
class Configurator extends \Nette\Configurator
{
	/** @var string */
	public $defaultConfigFile = '%appDir%/config.neon';
	
	/**
	 * Get initial instance of context.
	 * @return IContext
	 * @todo prepared for custom Context
	 *
	public function createContext()
	{
		$context = new Context;
		foreach ($this->defaultServices as $name => $service) {
			$context->addService($name, $service);
		}
		return $context;
	}*/
}