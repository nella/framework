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
 * @property string $pluralForm
 * @property array $metadata
 * @property-read \ArrayIterator $iterator
 */
class Dictionary extends \Nella\FreezableObject implements \IteratorAggregate, \Serializable
{
	const STATUS_SAVED = TRUE, 
		STATUS_TRANSLATED = FALSE, 
		STATUS_UNTRANSLATED = NULL;
	
	/** @var string */
	private $dir;
	/** @var IStorage */
	private $storage;
	/** @var string */
	private $lang;
	/** @var string */
	private $pluralForm;
	/** @var array */
	private $metadata;
	/** @var array */
	private $dictionary;

	/**
	 * @param string
	 * @param string
	 */
	public function __construct($dir, IStorage $storage)
	{
		$this->dir = $dir;
		$this->storage = $storage;
		$this->metadata = $this->dictionary = array();
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
		$this->updating();
		
		$this->metadata = $metadata;
		return $this;
	}
	
	/**
	 * @param string
	 * @param array
	 * @param bool
	 * @return Dictionary
	 */
	public function addTranslation($message, array $translation = array(), $status = self::STATUS_SAVED)
	{
		$this->dictionary[$message] = array(
			'status' => $status, 
			'translation' => $translation, 
		);
		
		return $this;
	}
	
	/**
	 * @param string
	 * @return bool
	 */
	public function hasTranslation($message)
	{
		return isset($this->dictionary[$message]);
	}
	
	/**
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->dictionary);
	}

	/**
	 * @param string
	 * @throws \Nette\InvalidStateException
	 */
	public function init($lang)
	{
		$this->updating();

		$this->lang = $lang;
		$this->storage->load($this->lang, $this);
		
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
			throw new \Nette\InvalidStateException("Dictionary not inicialized");
		}

		if (!$this->hasTranslation($message)) {
			return NULL;
		}

		$translation = $this->dictionary[$message]['translation'];
		$plural = $this->parsePluralForm($count);

		return isset($translation[$plural]) ? $translation[$plural] : $translation[0];
	}

	/**
	 * @param int
	 * @return int
	 */
	protected function parsePluralForm($form)
	{
		if (!isset($this->pluralForm) || $form === NULL) {
			return 0;
		}

		eval($x = preg_replace('/([a-z]+)/', '$$1', "n=$form;".$this->pluralForm.";"));

		return $plural;
	}
	
	/**
	 * @return Dictionary
	 */
	public function save()
	{
		$this->storage->save($this, $this->lang);
		return $this;
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
