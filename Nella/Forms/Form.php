<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Forms;

/**
 * Nella forms
 *
 * @author	Patrik Votoček
 */
class Form extends \Nette\Application\AppForm
{
	/**
	 * Application form constructor.
	 */
	public function __construct(\Nette\IComponentContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);
		$this->setup();
	}
	
	protected function setup()
	{
		// Setup columns
	}
	
	/*const DATE = 'Nella\Forms\Date::validate';
	const DATETIME = 'Nella\Forms\DateTime::validate';
	const TIME = 'Nella\Forms\Time::validate';*/

	/**
	 * Adds an email input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @param int	maximum number of characters the user may enter
	 * @return Nette\Forms\TextInput
	 */
	public function addEmail($name, $label = NULL, $cols = NULL, $maxLength = NULL)
	{
		$item = $this->addText($name, $label, $cols, $maxLength);
		$item->setAttribute('type', "email")->addCondition(self::FILLED)->addRule(self::EMAIL);
		return $item;
	}

	/**
	 * Adds an url input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @param int	maximum number of characters the user may enter
	 * @return Nette\Forms\TextInput
	 */
	public function addUrl($name, $label = NULL, $cols = NULL, $maxLength = NULL)
	{
		$item = $this->addText($name, $label, $cols, $maxLength);
		$item->setAttribute('type', "url")->addCondition(self::FILLED)->addRule(self::URL);
		return $item;
	}

	/**
	 * Adds a number input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	incremental number
	 * @param int	minimal value
	 * @param int	maximal value
	 * @return Nette\Forms\TextInput
	 */
	public function addNumber($name, $label = NULL, $step = 1, $min = NULL, $max = NULL)
	{
		$item = $this->addText($name, $label);
		$item->setAttribute('step', $step)->setAttribute('type', "number")
			->addCondition(self::FILLED)->addRule(self::NUMERIC);
		$range = array(NULL, NULL);
		if ($min !== NULL) {
			$item->setAttribute('min', $min);
			$range[0] = $min;
		}
		if ($max !== NULL) {
			$item->setAttribute('max', $max);
			$range[1] = $max;
		}
		if ($range != array(NULL, NULL)) {
			$item->addCondition(self::FILLED)->addRule(self::RANGE, NULL, $range);
		}
		
		return $item;
	}

	/**
	 * Adds a range input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	incremental number
	 * @param int	minimal value
	 * @param int	maximal value
	 * @return Nette\Forms\TextInput
	 */
	public function addRange($name, $label = NULL, $step = 1, $min = NULL, $max = NULL)
	{
		$item = $this->addNumber($name, $label, $step, $min, $max);
		return $item->setAttribute('type', "range");
	}

	/**
	 * Adds date input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @return Date
	 */
	public function addDate($name, $label = NULL, $cols = NULL)
	{
		return $this[$name] = new Date($label, $cols, NULL);
	}

	/**
	 * Adds a datetime input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @return DateTime
	 */
	public function addDateTime($name, $label = NULL, $cols = NULL)
	{
		return $this[$name] = new DateTime($label, $cols, NULL);
	}

	/**
	 * Adds a time input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @return Time
	 */
	public function addTime($name, $label = NULL, $cols = NULL)
	{
		return $this[$name] = new Time($label, $cols, NULL);
	}

	/**
	 * Adds search input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @param int	maximum number of characters the user may enter
	 * @return Nette\Forms\TextInput
	 */
	public function addSearch($name, $label = NULL, $cols = NULL, $maxLength = NULL)
	{
		$item = $this->addText($name, $label, $cols, $maxLength);
		return $item->setAttribute('type', "search");
	}

	/**
	 * Adds an editor input control to the form.
	 * 
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @param int	height of the control
	 * @return Nette\Forms\TextInput
	 */
	public function addEditor($name, $label = NULL, $cols = NULL, $rows = NULL)
	{
		$item = $this->addTextArea($name, $label, $cols, $rows);
		return $item->setAttribute('data-nella-editor', "data-nella-editor");
	}
	
	/**
	 * @return \Nella\DependencyInjection\IContext
	 */
	protected function getContext()
	{
		return $this->getPresenter()->context;
	}
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function getEntityManager()
	{
		return $this->getContext()->getService('Doctrine\ORM\EntityManager');
	}
	
	/**
	 * @param array
	 * @throws \InvalidStateException
	 */
	protected function processErrors(array $errors)
	{
		foreach ($errors as $name => $messages) {
			if (!isset($this[$name])) {
				throw new \InvalidStateException("Invalid value '$name' with messages '" . implode("', '", $messages) . "'");
			}
			foreach ($messages as $error) {
				$this[$name]->addError($error);
			}
		}
	}
}