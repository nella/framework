<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Localization;

class DummyTranslatorTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Localization\DummyTranslator */
	private $translator;

	public function setup()
	{
		$this->translator = new \Nella\Localization\DummyTranslator;
	}

	public function dataTranslate()
	{
		return array(
			array("simple untranslated text", "simple untranslated text"),
			array(array("untranslated text", "foo"), "untranslated text"),
			array(array("foo", "untranslated text"), "untranslated text", 0),
		);
	}

	/**
	 * @dataProvider dataTranslate
	 */
	public function testTranslate($message, $translation, $count = NULL)
	{
		$tmp = (array) $message;
		$tmp = reset($tmp);
		$this->assertEquals(
			$translation,
			$this->translator->translate($message, $count),
			"translate('$tmp')" . ($count !== NULL ? (" - " .$count) : "")
		);
	}
}
