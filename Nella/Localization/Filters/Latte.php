<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Localization\Filters;

use Nella\Localization\Dictionary;

/**
 * Latte translation extractor filter
 *
 * @author	Patrik Votoček
 */
class Latte extends \Nette\Object implements \Nella\Localization\IFilter
{
	/** @var array */
	public $exts = array("*.latte");
	
	/**
	 * @return \Nette\Latte\Parser
	 */
	protected function getParser()
	{
		$parser = new \Nette\Latte\Parser;
		$parser->handler = new LatteMacros;
		$parser->macros = LatteMacros::$defaultMacros;
		return $parser;
	}

	/**
	 * @param \Nella\Localization\Dictionary
	 */
	public function process(Dictionary $dictionary)
	{
		$dictionary->freeze();

		$latte = $this->getParser();

		$files = \Nette\Utils\Finder::findFiles($this->exts)->from($dictionary->dir);
		foreach ($files as $file) {
			$latte->parse(file_get_contents($file->getRealpath()));
			foreach ($latte->handler->translations as $message) {
				$translation = (array) $message;
				$message = is_array($message) ? reset($message) : $message;
				
				if ($dictionary->hasTranslation($message)) {
					continue;
				}

				$dictionary->addTranslation($message, $translation, Dictionary::STATUS_UNTRANSLATED);
			}
		}
	}
}
