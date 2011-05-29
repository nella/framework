<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella;

/**
 * Freezable object
 *
 * @author	Patrik Votoček
 */
abstract class FreezableObject extends \Nette\FreezableObject
{
	/** @var array */
	public $onFreeze = array();

	/**
	 * Freezes an array
	 *
	 * @return void
	 */
	public function freeze()
	{
		if (!$this->isFrozen()) {
			$this->onFreeze($this);
			parent::freeze();
		}
	}
}
