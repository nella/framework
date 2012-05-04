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
 * Application shutdown event args
 * 
 * @author Michael Moravec
 * 
 * @property-read \Exception $exception
 */
class ApplicationShutdown extends Application
{
	/** @var \Exception|NULL */
	private $exception;

	/**
	 * @param \Nette\Application\Application
	 * @param \Exception
	 */
	public function __construct(\Nette\Application\Application $application, \Exception $exception = NULL)
	{
		parent::__construct($application);
		$this->exception = $exception;
	}

	/**
	 * @return \Exception|NULL
	 */
	public function getException()
	{
		return $this->exception;
	}
}
