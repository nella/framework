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
 * Form time field item
 *
 * @author	Patrik Votoček
 */
class Time extends BaseDateTime
{
	/** @var string */
	public static $format = "G:i";

	/**
	 * @param string  control name
	 * @param string  label
	 * @param int  width of the control
	 * @param int  maximum number of characters the user may enter
	 */
	public function __construct($label = NULL, $cols = NULL, $maxLength = NULL)
	{
		parent::__construct($label, $cols, $maxLength);
		$this->control->type = "time";
		$this->control->setAttribute('data-nella-forms-time', $this->translateFormatToJs(static::$format));
	}
}