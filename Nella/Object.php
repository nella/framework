<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella;

/**
 * Nella Object
 *
 * @author	Patrik Votoček
 */
abstract class Object extends \Nette\Object
{
	/**
	 * Call to undefined method
	 * 
	 * @param  string  method name
	 * @param  array   arguments
	 * @return mixed
	 * @throws \Nette\MemberAccessException
	 */
	public function __call($name, $args)
	{
		return ObjectMixin::call($this, $name, $args);
	}
	
	/**
	 * Returns property value. Do not call directly
	 * 
	 * @param  string  property name
	 * @return mixed   property value
	 * @throws \Nette\MemberAccessException if the property is not defined.
	 */
	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}

	/**
	 * Sets value of a property. Do not call directly
	 * 
	 * @param  string  property name
	 * @param  mixed   property value
	 * @return void
	 * @throws \Nette\MemberAccessException if the property is not defined or is read-only
	 */
	public function __set($name, $value)
	{
		return ObjectMixin::set($this, $name, $value);
	}

	/**
	 * Is property defined?
	 * 
	 * @param  string  property name
	 * @return bool
	 */
	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}
}
