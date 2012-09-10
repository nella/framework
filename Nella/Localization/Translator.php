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
 * Translator
 *
 * @author	Patrik Votoček
 *
 * @property string $lang
 */
class Translator extends \Nette\Object implements \Nette\Localization\ITranslator
{
	/** @var string[]|array */
	private $modules;
	/** @var Dictionary[]|array */
	protected $dictionaries = array();
	/** @var IStorage */
	private $storage;
	/** @var string */
	private $pluralForm;
	/** @var string */
	private $lang = "en";

	/**
	 * @param IStorage
	 */
	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * @param string|NULL
	 * @return Translator
	 */
	public function addModule($module = NULL)
	{
		$this->modules[] = $module;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLang()
	{
		return $this->lang;
	}

	/**
	 * @param string
	 * @return Translator
	 */
	public function setLang($lang)
	{
		$this->lang = $lang;
		return $this;
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
	 * @return Translator
	 */
	public function setPluralForm($pluralForm)
	{
		$this->pluralForm = $pluralForm;
		return $this;
	}

	/**
	 * @throws \Nette\InvalidStateException
	 */
	public function init()
	{
		if (!$this->lang) {
			throw new \Nette\InvalidStateException('Language does not set');
		}

		if (!count($this->modules)) {
			$this->addModule();
		}

		foreach($this->modules as $module) {
			$this->dictionaries[] = $dictionary = $this->storage->load($this->lang, $module);
			if (!$this->pluralForm && !empty($dictionary->pluralForm)) {
				$this->pluralForm = $dictionary->pluralForm;
			}
		}
	}

	/**
	 * @param string|array
	 * @param int
	 * @return string
	 */
	public function translate($message, $count = NULL)
	{
		if (!count($this->dictionaries)) {
			$this->init();
		}

		$messages = (array) $message;
		$args = (array) $count;
		$form = $args ? reset($args) : NULL;
		$form = $form === NULL ? 1 : (is_int($form) ? $form : 0);
		$plural = $form == 1 ? 0 : 1;
		if ($this->pluralForm) {
			$form = Helper::toForm($this->pluralForm, $form);
		}

		$message = isset($messages[$plural]) ? $messages[$plural] : $messages[0];
		foreach ($this->dictionaries as $dictionary) {
			$translations = $dictionary->getTraslation(reset($messages));
			if ($translations !== FALSE) {
				$message = isset($translations[$form]) ? $translations[$form] : $message;
				break;
			}
		}

		if (count($args) > 0 && reset($args) !== NULL) {
			$message = str_replace(array("%label", "%name", "%value"), array("%%label", "%%name", "%%value"), $message);
			$message = vsprintf($message, $args);
		}

		return $message;
	}
}

