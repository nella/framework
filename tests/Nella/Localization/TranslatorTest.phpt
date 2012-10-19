<?php
/**
 * Test: Nella\Localization\Translator
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Localization\TranslatorTest
 */

namespace Nella\Tests\Localization;

use Assert;

require_once __DIR__ . '/../../bootstrap.php';
require_once MOCKS_DIR . '/Localization/Storages/Mock.php';

class TranslatorTest extends \TestCase
{
	/** @var \Nella\Localization\Translator */
	private $translator;

	public function setup()
	{
		$storage = new \Nella\Mocks\Localization\Storages\Mock(array(
			'simple translated text' => array("jednoduchy prelozeny text"),
			'translated text' => array("prelozeny text", "prelozene texty", "prelozenych textu"),
		), array(), 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4 ? 1 : 2);');
		$this->translator = new \Nella\Localization\Translator($storage);
	}

	public function testLang()
	{
		Assert::equal("en", $this->translator->getLang(), "->getLang() is default lang en");
		Assert::equal("en", $this->translator->lang, "->lang is default lang en");

		$this->translator->setLang("cs");
		Assert::equal("cs", $this->translator->getLang(), "->getLang() is lang cs");

		$this->translator->lang = "sk";
		Assert::equal("sk", $this->translator->lang, "->lang is lang sk");
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
		Assert::equal(
			$translation,
			$this->translator->translate($message, $count),
			"translate('$tmp')" . ($count !== NULL ? (" - " .$count) : "")
		);
	}
}
