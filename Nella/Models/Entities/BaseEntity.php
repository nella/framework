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
 * Basic entity
 * 
 * @mappedSuperclass
 *
 * @author	Patrik Votoček
 */
abstract class BaseEntity extends \Nette\Object
{
	public function __construct() { }
	
	public function check() { }
}