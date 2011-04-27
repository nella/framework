<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Localization;

/**
 * Dictionary
 *
 * @author	Patrik Votoček
 *
 * @property-read string $dir
 * @property-read string $module
 * @property-read array $metadata
 * @property-read array $dictionary
 */
class Dictionary extends \Nette\FreezableObject
{
	/** @var string */
	private $dir;
	/** @var string */
	private $module;
	/** @var array */
	private $metadata;
	/** @var array */
	private $dictionary;

	/**
	 * @param string
	 * @param string
	 */
	public function __construct($dir, $module = NULL)
	{
		$this->dir = $dir;
		$this->module = $module;
		$this->metadata = $this->dictionary = array();
	}

	/**
	 * @param string
	 * @throws \Nette\InvalidStateException
	 */
	public function loadLang($lang)
	{
		if ($this->isFrozen()) {
			throw new \Nette\InvalidStateException("Dictionary is already loaded");
		}

		$parser = new Parsers\Gettext;
		$path = $this->dir . "/" . $lang . ".mo";
		if (file_exists($path)) {
			$data = $parser->decode();
			$this->metadata = $data['metadata'];
			$this->dictionary = $data['dictionary'];
		}

		$this->freeze();
	}

	/**
	 * @param string
	 * @param int
	 * @return string
	 * @throws \Nette\InvalidStateException
	 */
	public function translate($message, $count = NULL)
	{
		if (!$this->isFrozen()) {
			throw new \Nette\InvalidStateException("Dictionary not loaded");
		}

		if (!isset($this->dictionary[$message])) {
			return NULL;
		}

		$translations = $this->dictionary[$message]['translation'];
		$plural = $this->getPluralForm($count);

		return isset($translations[$plural]) ? $translations[$plural] : $translations[0];
	}

	/**
	 * @param int
	 * @param int
	 */
	protected function getPluralForm($form)
	{
		if (!isset($this->metadata['Plural-Forms']) || $form === NULL) {
			return 0;
		}

		eval($x = preg_replace('/([a-z]+)/', '$$1', "n=$form;".$this->metadata['Plural-Forms'].";"));

		return $plural;
	}

	/**
	 * @return string
	 */
	public function getDir()
	{
		return $this->dir;
	}

	/**
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}

	/**
	 * @return array
	 */
	public function getDictionary()
	{
		return $this->dictionary;
	}
}
