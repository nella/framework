<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Models;

use Nette\ObjectMixin;

/**
 * Base entity repository
 *
 * @author	Patrik Votoček
 * 
 * @property-read \Doctrine\ORM\EntityManager $entityManager
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
 */
class Repository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * Does exist a entity with key equal to value?
	 * 
	 * @param string
	 * @param mixed
	 */
	public function doesExistByColumn($key, $value)
	{
		$res = $this->findOneBy(array($key => $value));
		return !empty($res);
	}
	
	/**
	 * Does exist a entity with key equal to value and does not same entity?
	 * 
	 * @param string
	 * @param string
	 * @param mixed
	 */
	public function isColumnUnique($id, $key, $value)
	{
		$res = $this->findOneBy(array($key => $value));
		return empty($res) ?: $res->id == $id;
	}
	
	/**
	 * Fetches all records equals array of ids
	 * 
	 * @param array
	 * @return array
	 */
	public function findByIds(array $ids)
	{
		$arr = array();
		foreach ($this->createQueryBuilder('uni')->where($qb->expr()->in('i.id', $ids))->getQuery()->getResult() as $res) {
			$arr[$res->id] = $res;
		}
		
		return $arr;
	}
	
	/**
	 * Fetches all records like $key => $value pairs
	 * 
	 * @param string
	 * @param string
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL)
	{
		$res = $this->createQueryBuilder('uni')->select("uni.$key, uni.$value")->getQuery()->getResult();
		
		$arr = array();
		foreach ($res as $row) {
			$arr[$row[$key]] = $row[$value];
		}
		
		return $arr;
	}
	
	/**
	 * Fetches all records and returns an associative array
	 * 
	 * @param string
	 * @return array
	 */
	public function fetchAssoc($key)
	{
		$res = $this->findAll();
		
		$arr = array();
		foreach ($res as $doc) {
			if (array_key_exists($doc->$key, $arr)) {
				throw new \InvalidStateException("Key value {$doc->{"get" . ucfirs($key)}} is duplicit in fetched associative array. Try to use different associative key");
			}
			$arr[$doc->{"get" . ucfirs($key)}()] = $doc;
		}
		
		return $arr;
	}
	
	/************************************************** Nette\Object implementation **************************************************/
	
	/**
	 * Access to reflection.
	 * @return Nette\Reflection\ClassReflection
	 */
	public static function getReflection()
	{
		return new \Nette\Reflection\ClassReflection(get_called_class());
	}

	/**
	 * Call to undefined method.
	 * @param  string  method name
	 * @param  array   arguments
	 * @return mixed
	 * @throws \MemberAccessException
	 * @throws \Doctrine\ODM\MongoDB\MongoDBException
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
	 * Call to undefined static method.
	 * @param  string  method name (in lower case!)
	 * @param  array   arguments
	 * @return mixed
	 * @throws \MemberAccessException
	 */
	public static function __callStatic($name, $args)
	{
		$class = get_called_class();
		throw new \MemberAccessException("Call to undefined static method $class::$name().");
	}

	/**
	 * Adding method to class.
	 * @param  string  method name
	 * @param  mixed   callback or closure
	 * @return mixed
	 */
	public static function extensionMethod($name, $callback = NULL)
	{
		if (strpos($name, '::') === FALSE) {
			$class = get_called_class();
		} else {
			list($class, $name) = explode('::', $name);
		}
		$class = new \Nette\Reflection\ClassReflection($class);
		if ($callback === NULL) {
			return $class->getExtensionMethod($name);
		} else {
			$class->setExtensionMethod($name, $callback);
		}
	}

	/**
	 * Returns property value. Do not call directly.
	 * @param  string  property name
	 * @return mixed   property value
	 * @throws \MemberAccessException if the property is not defined.
	 */
	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}

	/**
	 * Sets value of a property. Do not call directly.
	 * @param  string  property name
	 * @param  mixed   property value
	 * @return void
	 * @throws \MemberAccessException if the property is not defined or is read-only
	 */
	public function __set($name, $value)
	{
		return ObjectMixin::set($this, $name, $value);
	}

	/**
	 * Is property defined?
	 * @param  string  property name
	 * @return bool
	 */
	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}

	/**
	 * Access to undeclared property.
	 * @param  string  property name
	 * @return void
	 * @throws \MemberAccessException
	 */
	public function __unset($name)
	{
		throw new \MemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");
	}
}
