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
 * After compiler args
 *
 * @author    Patrik Votoček
 *
 * @property-read \Nette\Utils\PhpGenerator\ClassType $class
 */
class CompilerAfter extends Compiler
{
	/** @var \Nette\Utils\PhpGenerator\ClassType */
	private $class;

	/**
	 * @param \Nette\Config\Compiler
	 * @param \Nette\Utils\PhpGenerator\ClassType
	 */
	public function __construct(\Nette\Config\Compiler $compiler, \Nette\Utils\PhpGenerator\ClassType $class)
	{
		parent::__construct($compiler);
		$this->class = $class;
	}

	/**
	 * @return \Nette\Utils\PhpGenerator\ClassType
	 */
	final public function getClass()
	{
		return $this->class;
	}
}

