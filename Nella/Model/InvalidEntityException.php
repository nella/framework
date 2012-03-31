<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Model;

/**
 * Ivalid entity state exception
 *
 * @author	Patrik Votoček
 */
class InvalidEntityException extends Exception
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