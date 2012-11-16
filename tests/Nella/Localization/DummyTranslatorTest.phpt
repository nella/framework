<?php
/**
 * Test: Nella\Localization\DummyTranslator
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Localization;

use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class DummyTranslatorTest extends \Tester\TestCase
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
		Assert::equal(
			$translation,
			$this->translator->translate($message, $count),
			"translate('$tmp')" . ($count !== NULL ? (" - " .$count) : "")
		);
	}
}

id(new DummyTranslatorTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
