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

/**
 * Form datetime field item
 *
 * @author	Patrik Votoček
 */
class DateTime extends BaseDateTime
{
	/** @var string */
	public static $format = 'Y-n-j H:i';
	/** @var string */
	public static $dateFormat = 'Y-n-j';
	/** @var string */
	public static $timeFormat = 'H:i';

	/**
	 * @param string  control name
	 * @param string  label
	 * @param int  width of the control
	 * @param int  maximum number of characters the user may enter
	 */
	public function __construct($label = NULL, $cols = NULL, $maxLength = NULL)
	{
		parent::__construct($label, $cols, $maxLength);
		$this->control->type = 'datetime';
		$this->control->data('nella-forms-datetime', static::$format);
		$this->control->data('nella-forms-date', static::$dateFormat);
		$this->control->data('nella-forms-time', static::$timeFormat);
	}
}

