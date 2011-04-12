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
 * @property \DateTime $value
 */
abstract class BaseDateTime extends \Nette\Forms\TextInput
{
	/** @var string */
	public static $format = "Y-n-j";
	
	/** @var array */
	public static $formatPhpToJs = array(
		'd' => "dd", 
		'j' => "d", 
		'm' => "mm", 
		'n' => "m", 
		//'z' => "oo", ???
		'z' => "o", 
		'Y' => "yy", 
		'y' => "y", 
		'U' => "@", 
		'h' => "h", 
		'H' => "hh", 
		'g' => "g", 
		'A' => "TT", 
		'i' => "ii", 
		's' => "ss", 
		
	);
	
	/**
	 * @param string
	 * @return string
	 */
	protected function translateFormatToJs($format)
	{
		return str_replace(array_keys(static::$formatPhpToJs), array_values(static::$formatPhpToJs), $this->translate($format));
	}

	/**
	 * @return \DateTime|NULL
	 */
	public function getValue()
	{
		$value = parent::getValue();
		return $value ? \DateTime::createFromFormat(static::$format, $value) : NULL;
	}

	/**
	 * @param \DateTime
	 * @return Date
	 */
	public function setValue($value = NULL)
	{
		try {
			if ($value instanceof \DateTime) {
				return parent::setValue($value->format(static::$format));
			} else {
				return parent::setValue($value);
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