<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application\UI;

use \Nette\ComponentModel\IComponent;

/**
 * Component container interface
 * 
 * @author	Patrik Votocek
 */
interface IComponentContainer
{
	/**
	 * Adds the specified component
	 * 
	 * @param \Nette\ComponentModel\IComponent|\Nette\Callback|\Closure|array
	 * @param string
	 * @return void
	 */
	public function addComponent($name, $component);
	
	/**
	 * Has component registered
	 *
	 * @param string
	 * @return bool
	 */
	public function hasComponent($name);
	
	/**
	 * Returns single component
	 * 
	 * @param string
	 * @param \Nette\ComponentModel\IContainer
	 * @return \Nette\ComponentModel\IComponent|NULL
	 */
	public function getComponent($name, \Nette\ComponentModel\IContainer $parent);
}
