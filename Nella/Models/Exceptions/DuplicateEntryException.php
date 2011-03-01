<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Models;

/**
 * Duplicate column value exception
 *
 * @author	Patrik Votoček
 */
class DuplicateEntryException extends \InvalidStateException implements IException
{
	/** @var string */
	private $name;
	
	/**
	 * @param string
	 * @param string
	 * @param int
	 * @param \Exception
	 */
	public function __construct($name, $message = NULL, $code = NULL, \Exception $previous = NULL)
	{
		parent::__construct($message, $code, $previous);
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}