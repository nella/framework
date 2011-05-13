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
 * Duplicate entry exception
 *
 * @author	Dragon Jake
 */
class DuplicateEntryException extends \LogicException
{
	/** @var string */
	private $field;
	
	/**
	 * @param string
	 * @param string
	 */
	public function __construct($field, $message)
	{
		parent::__construct($message);
		$this->field = $field;
	}
	
	/**
	 * @return array
	 */
	public function getErrors()
	{
		return array($this->field => array($this->getMessage()));
	}
}
