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
			throw new \Nette\InvalidArgumentException("Class '$class' not exist");
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
	 * @throws \Nette\InvalidStateException
	 */
	public function addRule($name, $type, $data = NULL)
	{
		$mapper = function ($rule) {
			return $rule[0];
		};

		if (isset($this->rules[$name]) && in_array($type, array_map($mapper, $this->rules[$name]))) {
			throw new \Nette\InvalidStateException("Rule type '$type' for property '{$this->name}::\$$name' already exists");
		}

		if (!isset($this->rules[$name]) || !is_array($this->rules[$name])) {
			$this->rules[$name] = array(array($type, $data));
		} else {
			$this->rules[$name][] = array($type, $data);
		}

		return $this;
	}

	/**
	 * @return \Nette\Reflection\ClassType
	 */
	public function getClassReflection()
	{
		return new \Nette\Reflection\ClassType(strtolower($this->name));
	}
}