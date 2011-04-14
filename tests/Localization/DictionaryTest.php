<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Localization;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Localization\Dictionary */
	private $dictionary;
	
	public function setUp()
	{
		$this->dictionary = new \Nella\Localization\Dictionary(__DIR__);
	}
	
	public function testGetDir()
	{
		$this->assertEquals(__DIR__, $this->dictionary->getDir(), "->getDir() return dirname");
		$this->assertEquals(__DIR__, $this->dictionary->dir, "->dir resturn dirname");
	}

	public function testGetModule()
	{
		$this->assertNull($this->dictionary->getModule(), "->getModule() return default - NULL");
		$this->assertNull($this->dictionary->module, "->module return default - NULL");
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testLoadLangException()
	{
		$this->dictionary->loadLang("test");
		$this->dictionary->loadLang("test");
	}

	/**
	 * @expectedException \Nette\InvalidStateException
	 */
	public function testTranslateException()
	{
		$this->dictionary->translate("foo");
	}

	public function testTranslate()
	{
		$this->dictionary->loadLang("test");
		$this->assertNull($this->dictionary->translate("test"), "->translate() 'test' return NULL because in this dictionary not exist");
	}
}
