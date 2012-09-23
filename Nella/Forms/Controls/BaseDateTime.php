<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Forms\Controls;

use DateTime;

/**
 * Form date field item
 *
 * @author	Patrik Votoček
 *
 * @property \DateTime $value
 */
abstract class BaseDateTime extends \Nette\Forms\Controls\TextInput
{
	/** @var string */
	public static $format = 'Y-n-j';

	/**
	 * @return \DateTime|NULL
	 */
	public function getValue()
	{
		$value = parent::getValue();
		$value = DateTime::createFromFormat(static::$format, $value);
		$err = DateTime::getLastErrors();
		if ($err['error_count']) {
			$value = FALSE;
		}
		return $value ?: NULL;
	}

	/**
	 * @param \DateTime
	 * @return BaseDateTime
	 */
	public function setValue($value)
	{
		try {
			if ($value instanceof DateTime) {
				return parent::setValue($value->format(static::$format));
			} else {
				return parent::setValue($value);
			}
		} catch (\Exception $e) {
			return parent::setValue(NULL);
		}
	}

	/**
	 * @param BaseDateTime
	 * @return bool
	 */
	public static function validateValid(\Nette\Forms\IControl $control)
	{
		$value = $control->getValue();
		return (is_null($value) || $value instanceof DateTime);
	}

	/**
	 * @return bool
	 */
	public function isFilled()
	{
		return (bool) $this->getValue();
	}
}

