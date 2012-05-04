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
 * Application error event args
 * 
 * @author Michael Moravec
 * 
 * @property-read \Exception $exception
 */
class ApplicationError extends Application
{
	/** @var \Exception */
	private $exception;

	/**
	 * @param \Nette\Application\Application
	 * @param \Exception
	 */
	public function __construct(\Nette\Application\Application $application, \Exception $exception)
	{
		parent::__construct($application);
		$this->exception = $exception;
	}

	/**
	 * @return \Exception
	 */
	public function getException()
	{
		return $this->exception;
	}
}
