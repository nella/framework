<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Event\Args;

/**
 * Compiler event args
 *
 * @author    Patrik Votoček
 *
 * @property-read \Nette\Config\Compiler $compiler
 */
class Compiler extends \Nella\Event\EventArgs
{
	/** @var \Nette\Config\Compiler */
	private $compiler;

	/**
	 * @param \Nette\Config\Compiler
	 */
	public function __construct(\Nette\Config\Compiler $compiler)
	{
		$this->compiler = $compiler;
	}

	/**
	 * @return \Nette\Config\Compiler
	 */
	final public function getCompiler()
	{
		return $this->compiler;
	}
}
