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
 * Form date field item
 *
 * @author	Patrik Votoček
 */
class Date extends BaseDateTime
{
	/** @var string */
	public static $format = 'Y-n-j';

	/**
	 * @param string  control name
	 * @param string  label
	 * @param int  width of the control
	 * @param int  maximum number of characters the user may enter
	 */
	public function __construct($label = NULL, $cols = NULL, $maxLength = NULL)
	{
		parent::__construct($label, $cols, $maxLength);
		$this->control->type = 'date';
		$this->control->data('nella-forms-date', static::$format);
	}
}

