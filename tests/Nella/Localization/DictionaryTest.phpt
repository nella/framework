<?php
/**
 * Test: Nella\Localization\Dictionary
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

class DictionaryTest extends \Tester\TestCase
{
	/** @var \Nella\Localization\Dictionary */
	private $dictionary;

	public function setUp()
	{
		parent::setUp();
		$this->dictionary = new \Nella\Localization\Dictionary('cs_CZ', 'main');
	}

	public function testGetLang()
	{
		Assert::equal('cs_CZ', $this->dictionary->getLang(), '->getLang() return lang');
		Assert::equal('cs_CZ', $this->dictionary->lang, '->lang return lang');
	}

	public function testGetModule()
	{
		Assert::equal('main', $this->dictionary->getModule(), '->getModule() return module');
		Assert::equal('main', $this->dictionary->module, '->module return module');
	}

	public function testGetSetPluralForm()
	{
		$this->dictionary->setPluralForm('foo');
		Assert::equal('foo', $this->dictionary->getPluralForm(), '->getPluralForm return actual plural');
		Assert::equal('foo', $this->dictionary->pluralForm, '->pluralForm return actual plural');

		$this->dictionary->pluralForm = 'bar';
		Assert::equal('bar', $this->dictionary->getPluralForm(), '->getPluralForm return actual plural');
		Assert::equal('bar', $this->dictionary->pluralForm, '->pluralForm return actual plural');
	}

	public function testTranslations()
	{
		$this->dictionary->addTranslation('Foo', array('Bar'));
		$this->dictionary->addTranslation('Bar', array('Lorem', 'Ipsum', 'Dolor'));

		Assert::equal(array(
			'Foo' => array('Bar'),
			'Bar' => array('Lorem', 'Ipsum', 'Dolor'),
		), iterator_to_array($this->dictionary));
	}

	public function testGetTranslation()
	{
		$this->dictionary->addTranslation('foo', array('bar'));
		Assert::equal(array('bar'), $this->dictionary->getTraslation('foo'));
	}

	public function testGetUndefinedTranslation()
	{
		Assert::false($this->dictionary->getTraslation('undefined'));
	}

	public function testGetSetMetadata()
	{
		$metadata = array('foo' => 'bar');
		$this->dictionary->setMetadata($metadata);

		Assert::equal($metadata, $this->dictionary->getMetadata(), '->getMetadate() return metadata');
		Assert::equal($metadata, $this->dictionary->metadata, '->metadate return metadata');

		$metadata = array('bar' => 'foo');
		$this->dictionary->metadata = $metadata;

		Assert::equal($metadata, $this->dictionary->getMetadata(), '->getMetadate() return metadata');
		Assert::equal($metadata, $this->dictionary->metadata, '->metadate return metadata');
	}
}

id(new DictionaryTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
