<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Localization;

class TranslatorTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Localization\Translator */
	private $translator;

	public function setup()
	{
		$this->translator = new \Nella\Localization\Translator;
	}

	public function testDicionaries()
	{
		$this->assertEquals(0, count($this->translator->dictionaries), "->dictionaries default is not inicialized");

		$this->translator->addDictionary('test', __DIR__);
		$dictionaries = $this->translator->getDictionaries();
		$this->assertEquals(1, count($dictionaries), "->getDictionaries() after ->addDictionary() count 1 dictionary");

		$dictionary = reset($dictionaries);
		$this->assertInstanceOf('Nella\Localization\Dictionary', $dictionary, "is dictionary instance of localization dictionary");
		$this->assertEquals(__DIR__, $dictionary->getDir(), "is dictionary loaded valid dir");
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
		$this->injectTestDictionaryData();

		$tmp = (array) $message;
		$tmp = reset($tmp);
		$this->assertEquals(
			$translation,
			$this->translator->translate($message, $count),
			"translate('$tmp')" . ($count !== NULL ? (" - " .$count) : "")
		);
	}

	private function injectTestDictionaryData()
	{
		$this->translator->lang = "cs";
		$this->translator->freeze();

		$mock = new \Nella\Localization\Dictionary(__DIR__, new Storages\Mock(array(
			'simple translated text' => array("jednoduchy prelozeny text"),
			'translated text' => array("prelozeny text", "prelozene texty", "prelozenych textu"),
		)));
		$mock->setPluralForm('nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4 ? 1 : 2);');
		$mock->init('test');

		$ref = new \Nette\Reflection\Property('Nella\Localization\Translator', 'dictionaries');
		$ref->setAccessible(TRUE);
		$ref->setValue($this->translator, array($mock));
		$ref->setAccessible(FALSE);
	}
}
