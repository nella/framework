<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, 
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Event\Args;

/**
 * Before compiler args
 *
 * @author    Patrik Votoček
 *
 * @property-read \Nette\DI\ContainerBuilder $containerBuilder
 */
class CompilerBefore extends Compiler
{
	/** @var \Nette\DI\ContainerBuilder */
	private $containerBuilder;

	/**
	 * @param \Nette\Config\Compiler
	 * @param \Nette\DI\ContainerBuilder
	 */
	public function __construct(\Nette\Config\Compiler $compiler, \Nette\DI\ContainerBuilder $containerBuilder)
	{
		parent::__construct($compiler);
		$this->containerBuilder = $containerBuilder;
	}

	/**
	 * @return \Nette\DI\ContainerBuilder
	 */
	final public function getContainerBuilder()
	{
		return $this->containerBuilder;
	}
}

