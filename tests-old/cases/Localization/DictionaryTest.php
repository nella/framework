<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Localization;

use Nella\Localization\Dictionary;

class DictionaryTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Localization\Dictionary */
	private $dictionary;

	public function setup()
	{
		$this->dictionary = new Dictionary('cs_CZ', 'main');
	}

	public function testGetLang()
	{
		$this->assertEquals('cs_CZ', $this->dictionary->getLang(), '->getLang() return lang');
		$this->assertEquals('cs_CZ', $this->dictionary->lang, '->lang return lang');
	}

	public function testGetModule()
	{
		$this->assertEquals('main', $this->dictionary->getModule(), '->getModule() return module');
		$this->assertEquals('main', $this->dictionary->module, '->module return module');
	}

	public function testGetSetPluralForm()
	{
		$this->dictionary->setPluralForm('foo');
		$this->assertEquals('foo', $this->dictionary->getPluralForm(), '->getPluralForm return actual plural');
		$this->assertEquals('foo', $this->dictionary->pluralForm, '->pluralForm return actual plural');

		$this->dictionary->pluralForm = 'bar';
		$this->assertEquals('bar', $this->dictionary->getPluralForm(), '->getPluralForm return actual plural');
		$this->assertEquals('bar', $this->dictionary->pluralForm, '->pluralForm return actual plural');
	}

	public function testTranslations()
	{
		$this->dictionary->addTranslation('Foo', array('Bar'));
		$this->dictionary->addTranslation('Bar', array('Lorem', 'Ipsum', 'Dolor'));

		$this->assertEquals(array(
			'Foo' => array('Bar'),
			'Bar' => array('Lorem', 'Ipsum', 'Dolor'),
		), iterator_to_array($this->dictionary));
	}

	public function testGetTranslation()
	{
		$this->dictionary->addTranslation('foo', array('bar'));
		$this->assertEquals(array('bar'), $this->dictionary->getTraslation('foo'));
	}

	public function testGetUndefinedTranslation()
	{
		$this->assertFalse($this->dictionary->getTraslation('undefined'));
	}

	public function testGetSetMetadata()
	{
		$metadata = array('foo' => 'bar');
		$this->dictionary->setMetadata($metadata);

		$this->assertEquals($metadata, $this->dictionary->getMetadata(), '->getMetadate() return metadata');
		$this->assertEquals($metadata, $this->dictionary->metadata, '->metadate return metadata');

		$metadata = array('bar' => 'foo');
		$this->dictionary->metadata = $metadata;

		$this->assertEquals($metadata, $this->dictionary->getMetadata(), '->getMetadate() return metadata');
		$this->assertEquals($metadata, $this->dictionary->metadata, '->metadate return metadata');
	}
}
