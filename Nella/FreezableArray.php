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
 * Freezable array object
 *
 * @author	Patrik Votoček
 */
class FreezableArray extends \Nette\FreezableObject implements \ArrayAccess, \Countable, \IteratorAggregate
{
	/** @var array */
	private $list = array();
	/** @var array */
	public $onFreeze = array();

	/**
	 * Freezes an array
	 * @return void
	 */
	public function freeze()
	{
		if (!$this->isFrozen()) {
			$this->onFreeze($this);
			parent::freeze();
		}
	}

	/**
	 * Returns an iterator over all items.
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		$this->freeze();
		return new \ArrayIterator($this->list);
	}

	/**
	 * Returns items count.
	 * @return int
	 */
	public function count()
	{
		$this->freeze();
		return count($this->list);
	}

	/**
	 * Replaces or appends a item.
	 * @param  string
	 * @param  mixed
	 * @return FreezableArray
	 */
	public function offsetSet($key, $value)
	{
		if ($this->isFrozen()) {
			$class = get_called_class();
			throw new \InvalidStateException("Cannot set $key, because the $class has been frozen");
		}

		$this->list[$key] = $value;
		return $this;
	}

	/**
	 * Returns a item.
	 * @param  string
	 * @return mixed
	 * @throws \MemberAccessException
	 */
	public function offsetGet($key)
	{
		$this->freeze();
		if (!$this->offsetExists($key)) {
			$class = get_called_class();
			throw new \MemberAccessException("Cannot read an undeclared item {$class}['{$key}'].");
		}
		return $this->list[$key];
	}

	/**
	 * Determines whether a item exists.
	 * @param  string
	 * @return bool
	 */
	public function offsetExists($key)
	{
		$this->freeze();
		return array_key_exists($key, $this->list);
	}

	/**
	 * Removes the element at the specified position in this list.
	 * @param  string
	 * @return FreezableArray
	 */
	public function offsetUnset($key)
	{
		if ($this->isFrozen()) {
			$class = get_called_class();
			throw new \InvalidStateException("Cannot unset $keyn because the $class has been frozen");
		}
		unset($this->list[$key]);
		return $this;
	}
}
