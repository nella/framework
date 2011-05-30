<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Localization\Storages;

use Nella\Localization\Dictionary;

/**
 * @author	Patrik Votoček
 */
class Mock extends \Nette\Object implements \Nella\Localization\IStorage
{
	/** @var array */
	private $dictionary;
	/** @var array */
	private $metadata;
	
	/**
	 * @param array
	 * @param array
	 */
	public function __construct(array $dictionary = array(), array $metadata = array())
	{
		$this->dictionary = $dictionary;
		$this->metadata = $metadata;
	}
	
	/**
	 * @param string
	 * @param \Nella\Localization\Dictionary
	 */
	public function load($lang, Dictionary $dictionary)
	{
		foreach ($this->dictionary as $message => $translation) {
			$dictionary->addTranslation($message, $translation, Dictionary::STATUS_UNTRANSLATED);
		}
		$dictionary->metadata = $this->metadata;
	}

	/**
	 *
	 * @param \Nella\Localization\Dictionary
	 * @param string
	 */
	public function save(Dictionary $dictionary, $lang)
	{
		// nothing
	}
	
}
