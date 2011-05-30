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
 * Ivalid entity state exception
 *
 * @author	Patrik Votoček
 */
class NotValidEntityException extends Exception
{
	/** @var array */
	private $errors;

	/**
	 * @param string
	 * @param array
	 */
	public function __construct($message, array $errors)
	{
		parent::__construct($message);
		$this->errors = $errors;
	}

	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}