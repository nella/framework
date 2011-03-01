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
 * Form date field item
 *
 * @author	Patrik Votoček
 * 
 * @property DateTime $value
 */
class Date extends \Nette\Forms\TextInput
{
	/** @var string */
	public static $format = "Y-n-j";
	
	/**
	 * @param  string  control name
	 * @param  string  label
	 * @param  int  width of the control
	 * @param  int  maximum number of characters the user may enter
	 */
	public function __construct($label = NULL, $cols = NULL, $maxLength = NULL)
	{
		parent::__construct($label, $cols, $maxLength);
		$this->control->type = "date";
		$this->control->setAttribute('data-nella-forms-date', $this->translate(static::$format));
		//$this->addCondition(Form::FILLED)->addRule(Form::DATE);
	}
	
	/**
	 * @return DateTime
	 */
	public function getValue()
	{
		$value = parent::getValue();
		return $value ? \DateTime::createFromFormat(static::$format, $value) : NULL;
	}
	
	/**
	 * @param DateTime
	 * @return Date
	 */
	public function setValue($value = NULL)
	{
		if (!($value instanceof \DateTime) && $value !== NULL) {
			throw new \InvalidArgumentException("Value must be DateTime or NULL");
		}
		
		try {
			if ($value === NULL) {
				return parent::setValue(NULL);
			} else {
				return parent::setValue($value->format(static::$format));
			}
		} catch (\Exception $e) {
			return parent::setValue(NULL);
		}
	}
	
	/**
	 * @param Date
	 * @return bool
	 */
	public static function validateValid(\Nette\Forms\IFormControl $control)
	{
		$value = $this->getValue();
		return (is_null($value) || $value instanceof \DateTime);
	}
}
