<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Forms\Controls;

/**
 * Form datetime field item
 *
 * @author	Patrik Votoček
 */
class DateTime extends BaseDateTime
{
	/** @var string */
	public static $format = "Y-n-j G:i";
	/** @var string */
	public static $dateFormat = "Y-n-j";
	/** @var string */
	public static $timeFormat = "G:i";

	/**
	 * @param string  control name
	 * @param string  label
	 * @param int  width of the control
	 * @param int  maximum number of characters the user may enter
	 */
	public function __construct($label = NULL, $cols = NULL, $maxLength = NULL)
	{
		parent::__construct($label, $cols, $maxLength);
		$this->control->type = "datetime";
		$this->control->setAttribute('data-nella-forms-date', $this->translateFormatToJs(static::$dateFormat));
		$this->control->setAttribute('data-nella-forms-time', $this->translateFormatToJs(static::$timeFormat));
		//$this->addCondition(Form::FILLED)->addRule(Form::DATETIME);
	}
}