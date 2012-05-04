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
 * General Application event args
 * 
 * @author Michael Moravec
 * 
 * @property-read \Nette\Application\Application
 */
class Application extends \Nella\Event\EventArgs
{
	/** @var \Nette\Application\Application */
	private $application;

	/**
	 * @param \Nette\Application\Application
	 */
	public function __construct(\Nette\Application\Application $application)
	{
		$this->application = $application;
	}

	/**
	 * @return \Nette\Application\Application
	 */
	final public function getApplication()
	{
		return $this->application;
	}
}
