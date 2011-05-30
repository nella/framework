<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Models;

/**
 * Duplicate entry exception
 *
 * @author	Patrik Votoček
 */
class DuplicateEntryException extends Exception
{
	/** @var string */
	private $column;

	/**
	 * @param string
	 * @param string
	 */
	public function __construct($message, $column = NULL, \Exception $parent = NULL)
	{
		parent::__construct($message, 0, $parent);
		$this->column = $column;
	}

	/**
	 * @return column
	 */
	public function getColumn()
	{
		return $this->column;
	}
}