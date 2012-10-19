<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Mocks\Application\UI;

class Presenter extends \Nella\Application\UI\Presenter
{
	/**
	 * @param string
	 * @return \Nette\Application\UI\Presenter
	 */
	public function setName($name)
	{
		$ref = new \Nette\Reflection\Property('Nette\ComponentModel\Component', 'name');
		$ref->setAccessible(TRUE);
		$ref->setValue($this, $name);
		$ref->setAccessible(FALSE);
		return $this;
	}
}