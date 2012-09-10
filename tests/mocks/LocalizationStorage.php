<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Localization\Storages;

use Nella\Localization\Dictionary;

/**
 * @author	Patrik Votoček
 */
class StorageMock extends \Nette\Object implements \Nella\Localization\IStorage
{
	/** @var array */
	private $dictionary;
	/** @var array */
	private $metadata;
	/** @var string */
	private $pluralForm;

	/**
	 * @param array
	 * @param array
	 * @param string
	 */
	public function __construct(array $dictionary = array(), array $metadata = array(), $pluralForm = NULL)
	{
		$this->dictionary = $dictionary;
		$this->metadata = $metadata;
		$this->pluralForm = $pluralForm;
	}

	/**
	 * @param string
	 * @parma string|NULL
	 * @return \Nella\Localization\Dictionary
	 */
	public function load($lang, $module = NULL)
	{
		$dictionary = new Dictionary($lang, $module);
		if ($this->pluralForm) {
			$dictionary->setPluralForm($this->pluralForm);
		}

		foreach ($this->dictionary as $message => $translation) {
			$dictionary->addTranslation($message, $translation);
		}
		$dictionary->metadata = $this->metadata;

		return $dictionary;
	}

	/**
	 * @param \Nella\Localization\Dictionary
	 */
	public function save(Dictionary $dictionary)
	{
		// nothing
	}

}
