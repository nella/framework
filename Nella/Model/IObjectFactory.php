<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Model;

/**
 * Object Factory interface
 *
 * @author	Patrik Votoček
 */
interface IObjectFactory
{
	/**
	 * @param array
	 * @return object
	 */
	public function create(array $values = array());
}