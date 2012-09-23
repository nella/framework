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

use Nette\DI\Container as NContainer;

/**
 * General Container event args
 * 
 * @author Patrik Votoček
 * 
 * @property-read \Nette\DI\Container $container
 */
class Container extends \Nella\Event\EventArgs
{
	/** @var \Nette\DI\Container */
	private $container;

	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(NContainer $container)
	{
		$this->container = $container;
	}

	/**
	 * @return \Nette\DI\Container
	 */
	final public function getContainer()
	{
		return $this->container;
	}
}

