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
 */
class DummyTranslator extends \Nette\Object implements \Nette\Localization\ITranslator
{
	/**
	 * @param string|array
	 * @param int
	 * @return string
	 */
	public function translate($message, $count = NULL)
	{
		$messages = (array) $message;
		$args = (array) $count;
		$form = $args ? reset($args) : NULL;
		$form = $form === NULL ? 1 : (is_int($form) ? $form : 0);
		$plural = $form == 1 ? 0 : 1;

		$message = isset($messages[$plural]) ? $messages[$plural] : $messages[0];

		if (count($args) > 0 && reset($args) !== NULL) {
			$message = str_replace(array("%label", "%name", "%value"), array("%%label", "%%name", "%%value"), $message);
			$message = vsprintf($message, $args);
		}

		return $message;
	}
}

