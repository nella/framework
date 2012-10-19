<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Mocks\Localization\Storages;

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
