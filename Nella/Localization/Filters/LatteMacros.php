<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Localization\Filters;

/**
 * Latte macros for parse translator
 *
 * @author	Patrik Votoček
 *
 * @internal
 *
 * @property-read array $translations
 */
class LatteMacros extends \Nette\Latte\DefaultMacros
{
	/** @var array */
	private $translations = array();

	/**
	 * @param mixed $var
	 * @param mixed $modifiers
	 * @return string
	 */
	public function macroTranslate($var, $modifiers)
	{
		$x = $this->formatMacroArgs($var);
		$x = "\$this->addTranslation(" . $x . ");";
		eval($x); // please don't slap me
	}

	/**
	 * @param string|array
	 * @param int
	 */
	public function addTranslation($message, $count = NULL)
	{
		$this->translations[] = $message;
	}

	/**
	 * @return array
	 */
	public function getTranslations()
	{
		return $this->translations;
	}
}


