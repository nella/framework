<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Localization;

use Nella\Localization\Dictionary;

class DictionaryTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Localization\Dictionary */
	private $dictionary;

	public function setup()
	{
		$this->dictionary = new Dictionary(__DIR__, new Storages\Mock);
	}

	public function testGetDir()
	{
		$this->assertEquals(__DIR__, $this->dictionary->getDir(), "->getDir() return dirname");
		$this->assertEquals(__DIR__, $this->dictionary->dir, "->dir resturn dirname");
	}

	public function testGetPluralForm()
	{
		$this->assertNull($this->dictionary->getPluralForm(), "->getPluralForm return default - NULL");
		$this->assertNull($this->dictionary->pluralForm, "->pluralForm return default - NULL");
	}

	public function testSetPluralForm()
	{
		$this->dictionary->setPluralForm('foo');
		$this->assertEquals('foo', $this->dictionary->getPluralForm(), "->getPluralForm return actual plural");
		$this->assertEquals('foo', $this->dictionary->pluralForm, "->pluralForm return actual plural");

		$this->dictionary->pluralForm = 'bar';
		$this->assertEquals('bar', $this->dictionary->getPluralForm(), "->getPluralForm return actual plural");
		$this->assertEquals('bar', $this->dictionary->pluralForm, "->pluralForm return actual plural");
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testDoubleInitException()
	{
		$this->dictionary->init("test");
		$this->dictionary->init("test");
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testNotInitTranslateException()
	{
		$this->dictionary->translate("foo");
	}

	public function testAddTranslation()
	{
		$this->dictionary->addTranslation("Foo", array("Bar"));
		$this->dictionary->addTranslation("Bar", array("Foo"), Dictionary::STATUS_UNTRANSLATED);
		$this->dictionary->init('test');

		$this->assertEquals(array(
			'Foo' => array(
				'status' => Dictionary::STATUS_SAVED,
				'translation' => array(
					0 => "Bar",
				),
			),
			'Bar' => array(
				'status' => Dictionary::STATUS_UNTRANSLATED,
				'translation' => array(
					0 => "Foo",
				),
			),
		), $this->dictionary->iterator->getArrayCopy());
	}

	public function testTranslate()
	{
		$this->dictionary->init("test");
		$this->assertNull($this->dictionary->translate("test"), "->translate() 'test' return NULL because in this dictionary not exist");
	}

	public function testTranslate2()
	{
		$this->dictionary->addTranslation("Foo", array("Bar"));
		$this->dictionary->addTranslation("Bar", array("Foo"), Dictionary::STATUS_UNTRANSLATED);
		$this->dictionary->init('test');

		$this->assertEquals("Bar", $this->dictionary->translate("Foo"), "->translate('Foo') return 'Bar'");
		$this->assertEquals("Foo", $this->dictionary->translate("Bar"), "->translate('Bar') return 'Foo'");
	}

	public function testTranslate3()
	{
		$this->dictionary->addTranslation("Foo", array("Bar", "Baz"));
		$this->dictionary->pluralForm = 'nplurals=2; plural=(n==1) ? 0 : 1';
		$this->dictionary->init('test');

		$this->assertEquals("Bar", $this->dictionary->translate("Foo", 1), "->translate('Foo', 1) return 'Bar'");
		$this->assertEquals("Baz", $this->dictionary->translate("Foo", 2), "->translate('Foo', 2) return 'Baz'");
	}

	public function testMetadata()
	{
		$dictionary = new Dictionary(__DIR__, new Storages\Mock(array(), array('foo')));
		$dictionary->init('test');
		$this->assertEquals(array('foo'), $dictionary->getMetadata(), "->getMetadata() return array('foo')");
		$this->assertEquals(array('foo'), $dictionary->metadata, "->metadata return array('foo')");
	}

	public function testSave()
	{
		$this->markTestSkipped("Not implemented");
	}
}
