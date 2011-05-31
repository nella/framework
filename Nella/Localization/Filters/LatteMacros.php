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
class LatteMacros extends \Nette\Latte\Macros\MacroSet
{
	/** @var array */
	private $translations = array();

	/**
	 * @param \Nette\Latte\Parser
	 */
	public static function install(\Nette\Latte\Parser $parser)
	{
		$me = new static($parser);

		// _
		$me->addMacro('_', array($me, 'macroTranslate'));

		return $me;
	}

	/**
	 * @param \Nette\Latte\MacroNode
	 * @param mixed
	 * @return string
	 */
	public function macroTranslate(\Nette\Latte\MacroNode $node, $writer)
	{
		$x = $writer->formatArgs();
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


