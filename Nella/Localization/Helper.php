<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Localization;

/**
 * Localization helper
 *
 * @author	Patrik Votoček
 */
class Helper extends \Nette\Object
{
	public function __construct()
	{
		throw new \Nette\StaticClassException;
	}

	/**
	 * @param plural form
	 * @param int
	 * @return int
	 */
	public static function toForm($forms, $count)
	{
		eval($x = preg_replace('/([a-z]+)/', '$$1', "n=$count;".$forms.';'));
		return $plural;
	}
}

