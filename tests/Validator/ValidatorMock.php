<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Validator;

class ValidatorMock extends \Nella\Validator\Validator
{
	public function getValidator($id)
	{
		$ref = new \Nette\Reflection\PropertyReflection('Nella\Validator\Validator', 'validators');
		$ref->setAccessible(TRUE);
		$validators = $ref->getValue($this);
		$ref->setAccessible(FALSE);
		return $validators[$id];
	}
}
