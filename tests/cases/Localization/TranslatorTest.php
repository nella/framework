<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Localization;

class TranslatorTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Localization\Translator */
	private $translator;

	public function setup()
	{
		$this->translator = new \Nella\Localization\Translator(new Storages\StorageMock(array(
				'simple translated text' => array("jednoduchy prelozeny text"),
				'translated text' => array("prelozeny text", "prelozene texty", "prelozenych textu"),
		), array(), 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4 ? 1 : 2);'));
	}

	public function testLang()
	{
		$this->assertEquals("en", $this->translator->getLang(), "->getLang() is default lang en");
		$this->assertEquals("en", $this->translator->lang, "->lang is default lang en");

		$this->translator->setLang("cs");
		$this->assertEquals("cs", $this->translator->getLang(), "->getLang() is lang cs");

		$this->translator->lang = "sk";
		$this->assertEquals("sk", $this->translator->lang, "->lang is lang sk");
	}

	public function dataTranslate()
	{
		return array(
			array("simple untranslated text", "simple untranslated text"),
			array(array("untranslated text", "foo"), "untranslated text"),
			array(array("foo", "untranslated text"), "untranslated text", 0),
			array("simple translated text", "jednoduchy prelozeny text"),
			array(array("translated text", "foo"), "prelozeny text"),
			array(array("translated text", "foo"), "prelozene texty", 3),
			array(array("translated text", "foo"), "prelozenych textu", 0),
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
