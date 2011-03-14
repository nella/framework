<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Validator;

/**
 * Object property validator interface
 *
 * @author	Patrik Votoček
 */
interface IValidator
{
	/**
	 * @param mixed
	 * @return array
	 */
	public function validate($input);
}