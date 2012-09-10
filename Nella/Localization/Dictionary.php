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
 * Dictionary
 *
 * @author	Patrik Votoček
 *
 * @property-read string $lang
 * @property-read string|NULL $module
 * @property string $pluralForm
 * @property array $metadata
 * @property-read \ArrayIterator $iterator
 */
class Dictionary extends \Nette\Object implements \IteratorAggregate, \Serializable
{
	/** @var string */
	private $lang;
	/** @var string|NULL */
	private $module;
	/** @var string|NULL */
	private $pluralForm;
	/** @var array */
	private $metadata;
	/** @var array */
	private $dictionary;

	/**
	 * @param string
	 * @param string|NULL
	 */
	public function __construct($lang, $module = NULL)
	{
		$this->lang = $lang;
		$this->module = $module;
		$this->metadata = $this->dictionary = array();
	}

	/**
	 * @return string
	 */
	public function getLang()
	{
		return $this->lang;
	}

	/**
	 * @return NULL|string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * @return string
	 */
	public function getPluralForm()
	{
		return $this->pluralForm;
	}

	/**
	 * @param string
	 * @return Dictionary
	 */
	public function setPluralForm($pluralForm)
	{
		$this->pluralForm = $pluralForm;
		return $this;
	}

	/**
	 * @internal
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}

	/**
	 * @internal
	 * @param array
	 * @return Dictionary
	 */
	public function setMetadata(array $metadata = array())
	{
		$this->metadata = $metadata;
		return $this;
	}

	/**
	 * @param string
	 * @param array
	 * @return Dictionary
	 */
	public function addTranslation($message, array $translations = array())
	{
		$this->dictionary[$message] = $translations;
		return $this;
	}

	/**
	 * @param string
	 * @return array|false
	 */
	public function getTraslation($message)
	{
		if (!array_key_exists($message, $this->dictionary)) {
			return FALSE;
		}
		return $this->dictionary[$message];
	}

	/**
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->dictionary);
	}

	/**
	 * @return string
	 */
	public function serialize()
	{
		return serialize(array(
			'metadata' => $this->metadata,
			'pluralForm' => $this->pluralForm,
			'dicionary' => $this->dictionary,
		));
	}

	/**
	 * @param string
	 */
	public function unserialize($serialized)
	{
		$data = unserialize($serialized);
		$this->metadata = $data['metadata'];
		$this->pluralForm = $data['pluralForm'];
		$this->dictionary = $data['dictionary'];
	}
}

