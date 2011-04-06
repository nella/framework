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
 * Translator
 *
 * @author	Patrik Votoček
 *
 * @property string $lang
 */
class Translator extends \Nette\FreezableObject implements \Nette\ITranslator
{
	/** @var array */
	protected $dictionaries = array();
	/** @var string */
	private $lang = "en";

	/**
	 * @param string
	 * @param string
	 */
	public function addDictionary($dir, $module = NULL)
	{
		$this->dictionaries[] = new Dictionary($dir, $module);
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
	 * @throws \InvalidStateExcetion
	 */
	public function setLang($lang)
	{
		if ($this->isFrozen()) {
			throw new \InvalidStateException("Dictionaries are already loaded");
		}

		$this->lang = $lang;
		return $this;
	}

	private function load()
	{
		foreach ($this->dictionaries as $dictionary) {
			$dictionary->loadLang($this->lang);
		}
		$this->freeze();
	}

	/**
	 * @param string
	 * @param int
	 * @return string
	 */
	public function translate($message, $count = NULL)
	{
		if (!$this->isFrozen()) {
			$this->load();
		}

		$messages = is_array($message) ? $message : array($message);
		$args = is_array($count) ? $count : array($count);
		$form = current($args);
		$form = $form === NULL ? 1 : (is_int($form) ? $form : 0);
		$plural = $form == 1 ? 0 : 1;


		$message = isset($messages[$plural]) ? $messages[$plural] : $messages[0];
		foreach ($this->dictionaries as $dictionary) {
			if (($tmp = $dictionary->translate($messages[0], $form)) !== NULL) {
				$message = $tmp;
				break;
			}
		}

		if (count($args) > 0 && current($args) !== NULL) {
			$message = str_replace(array("%label", "%name", "%value"), array("%%label", "%%name", "%%value"), $message);
			vsprintf($message, $args);
		}

		return $message;
	}
}
