<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine;

/**
 * Basic entity repository
 *
 * @author	Patrik Votoček
 */
class Repository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * Reindex result by key
	 *
	 * @param string
	 * @param array
	 * @return array
	 */
	protected function reindexByKey($key, array $res)
	{
		$data = array();
		foreach ($res as $row) {
			$data[callback($row, 'get'.ucfirst($key))->invoke()] = $row;
		}

		return $data;
	}

	/**
	 * Fetches all records that correspond to ids of a given array
	 *
	 * @param array
	 * @param bool
	 * @return array
	 */
	public function findByIds(array $ids, $indexById = TRUE)
	{
		$id = reset($this->getClassMetadata()->identifier);
		$qb = $this->createQueryBuilder('e');
		$qb->where($qb->expr()->in("e.$id", $ids));
		$res = $qb->getQuery()->getResult();

		if ($indexById) {
			$res = $this->reindexByKey($id, $res);
		}

		return $res;
	}

	/**
	 * Does an entity with a key equal to value exist?
	 *
	 * @param string
	 * @param mixed
	 * @return bool
	 */
	public function doesExistByColumn($key, $value)
	{
		$res = $this->findOneBy(array($key => $value));
		return !empty($res);
	}

	/**
	 * Does an entity with key equal to value exist and is not same as given entity id?
	 *
	 * @param string
	 * @param string
	 * @param mixed
	 * @return bool
	 */
	public function isColumnUnique($id, $key, $value)
	{
		$res = $this->findOneBy(array($key => $value));
		return empty($res) ?: $res->id == $id;
	}

	/**
	 * Fetch all data with format to $row[$key] = $value
	 *
	 * @param string
	 * @param string
	 * @return array
	 */
	public function fetchPairs($key, $value)
	{
		$qb = $this->createQueryBuilder('e')->select("e.$key, e.$value");
		$res = $qb->getQuery()->getScalarResult();

		$data = array();
		foreach ($res as $row) {
			$data[$row[$key]] = $row[$value];
		}

		return $data;
	}

	/**
	 * Fetches all records and returns an associative array indexed by key
	 *
	 * @param string
	 * @return array
	 */
	public function fetchAssoc($key)
	{
		return $this->reindexByKey($key, $this->findAll());
	}

	/************************************* Nette\Object ********************************************/

	/**
	 * Access to reflection
	 *
	 * @return \Nette\Reflection\ClassType
	 */
	public static function getReflection()
	{
		return new \Nette\Reflection\ClassType(get_called_class());
	}

	/**
	 * Call to undefined method
	 *
	 * @param string  method name
	 * @param array   arguments
	 * @return mixed
	 * @throws \Nette\MemberAccessException
	 */
	public function __call($name, $args)
	{
		try {
			return parent::__call($name, $args);
		} catch (\BadMethodCallException $e) {
			return ObjectMixin::call($this, $name, $args);
		}
	}

	/**
	 * Call to undefined static method
	 *
	 * @param string  method name (in lower case!)
	 * @param array   arguments
	 * @return mixed
	 * @throws \Nette\MemberAccessException
	 */
	public static function __callStatic($name, $args)
	{
		return ObjectMixin::callStatic(get_called_class(), $name, $args);
	}

	/**
	 * Adding method to class
	 *
	 * @param string  method name
	 * @param mixed   callback or closure
	 * @return mixed
	 */
	public static function extensionMethod($name, $callback = NULL)
	{
		if (strpos($name, '::') === FALSE) {
			$class = get_called_class();
		} else {
			list($class, $name) = explode('::', $name);
		}
		$class = new \Nette\Reflection\ClassType($class);
		if ($callback === NULL) {
			return $class->getExtensionMethod($name);
		} else {
			$class->setExtensionMethod($name, $callback);
		}
	}

	/**
	 * Returns property value. Do not call directly
	 *
	 * @param string  property name
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
	 * @param string  property name
	 * @param mixed   property value
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
	 * @param string  property name
	 * @return bool
	 */
	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}

	/**
	 * Access to undeclared property
	 *
	 * @param string  property name
	 * @return void
	 * @throws \Nette\MemberAccessException
	 */
	public function __unset($name)
	{
		throw new \Nette\MemberAccessException("Cannot unset the property {$this->getReflection()->name}::\$$name.");
	}
}