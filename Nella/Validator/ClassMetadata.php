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
 * Classmetada metadata
 *
 * @author	Patrik Votoček
 * 
 * @property-read string $name
 * @property-read string $parent
 * @property-read array $rules
 * @property-read \Nella\Reflection\ClassReflection $classReflection
 */
class ClassMetadata extends \Nette\Object
{
	/** @var string */
	private $name;
	/** @var string */
	private $parent = NULL;
	/** @var array */
	private $rules = array();
	
	/**
	 * @param string
	 */
	public function __construct($class)
	{
		if (!class_exists(strtolower($class))) {
			throw new \InvalidArgumentException("Class '$class' not exist");
		}
		
		$this->name = $class;
		
		$parent = $this->getClassReflection()->getParentClass();
		$this->parent = $parent ? $parent->getName() : NULL;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @return string
	 */
	public function getParent()
	{
		return $this->parent;
	}
	
	/**
	 * @return array
	 */
	public function getRules()
	{
		return $this->rules;
	}
	
	/**
	 * @param string
	 * @param string
	 * @param array
	 * @return ClassMetadata
	 */
	public function addRule($name, $type, $data = NULL)
	{
		$lower = strtolower($name);
		
		$mapper = function ($rule) {
			return $rule[0];
		};
		if (isset($this->rules[$lower]) && in_array($type, array_map($mapper, $this->rules[$lower]))) {
			throw new \InvalidStateException("Rule type '$type' for property '{$this->name}::\$$name' is exist");
		}
		
		if (!isset($this->rules[$lower]) || !is_array($this->rules[$lower])) {
			$this->rules[$lower] = array(array($type, $data));
		} else {
			$this->rules[$lower][] = array($type, $data);
		}
		
		return $this;
	}
	
	/**
	 * @return \Nette\Reflection\ClassReflection
	 */
	public function getClassReflection()
	{
		return new \Nette\Reflection\ClassReflection(strtolower($this->name));
	}
}