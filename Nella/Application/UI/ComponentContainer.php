<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application\UI;

/**
 * ...
 * 
 * @author	Patrik Votocek
 */
class ComponentContainer extends \Nette\Object implements IComponentContainer
{
	/** @var array */
	private $components = array();
	
	/**
	 * Adds the specified component
	 * 
	 * @param \Nette\ComponentModel\IComponent|\Nette\Callback|\Closure|array
	 * @param string
	 * @return void
	 */
	public function addComponent($name, $component)
	{
		$this->components[$name] = $component;
	}
	
	/**
	 * Has component registered
	 *
	 * @param string
	 * @return bool
	 */
	public function hasComponent($name)
	{
		return isset($this->components[$name]);
	}
	
	/**
	 * Returns single component
	 * 
	 * @param string
	 * @param \Nette\ComponentModel\IContainer
	 * @return \Nette\ComponentModel\IComponent|NULL
	 * @throws \Nette\InvalidStateException
	 */
	public function getComponent($name, \Nette\ComponentModel\IContainer $parent = NULL)
	{
		if (!$this->hasComponent($name)) {
			throw new \Nette\InvalidStateException("Component with name '$name' does not exist");
		}
		
		if ($this->components[$name] instanceof \Nette\ComponentModel\IComponent) {
			return $this->components[$name];
		} else {
			return callback($this->components[$name])->invoke($parent, $name);
		}
	}
}
