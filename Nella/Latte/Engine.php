<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Latte;

/**
 * Nella latte engine
 *
 * @author	Patrik Votoček
 */
class Engine extends \Nette\Latte\Engine
{
	public function __construct(\Nette\DI\IContainer $container)
	{
		$this->parser = new \Nette\Latte\Parser;
		$this->parser->handler = $container->getService('macros');
		$class = get_class($this->parser->handler);
		$this->parser->macros = $class::$defaultMacros;
	}
}
