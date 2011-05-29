<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Localization\Storages;

use Nella\Localization\Dictionary;

require_once __DIR__ . "/../../bootstrap.php";

class GettextTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Localization\Storages\Gettext */
	private $storage;

	public function setUp()
	{
		$this->storage = new \Nella\Localization\Storages\Gettext(__DIR__ . "/GettextTest.mo");
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Localization\IStorage', $this->storage, "is instance of 'Nella\\Localization\\IStorage'");
	}

	public function testLoad()
	{
		$dictionary = new Dictionary(__DIR__, $this->storage);
		$this->storage->load("test", $dictionary);

		$this->assertEquals(array(
			'Plural-Forms' => "nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4 ? 1 : 2);",
			'Project-Id-Version' => "Čeština pro Nellu",
			'Report-Msgid-Bugs-To' => "bugs@nellacms.com",
			'POT-Creation-Date' => "2009-07-01 11:07+0100",
			'PO-Revision-Date' => "2011-02-07 23:40+0100",
			'Last-Translator' => "Patrik Votoček <patrik@votocek.cz>",
			'MIME-Version' => "1.0",
			'Content-Type' => "text/plain; charset=UTF-8",
			'Content-Transfer-Encoding' => "8bit",
			'X-Poedit-Language' => "Czech",
			'X-Poedit-Country' => "CZECH REPUBLIC",
			'X-Poedit-SourceCharset' => "utf-8",
			'X-Poedit-KeywordsList' => "__;_n;_x;_nx;_;!_",
			'Language-Team:' => NULL,
		), $dictionary->metadata, "metadata");
		
		//dump($dictionary->iterator);
		//exit(255)

		$this->assertEquals(array(
			'Test' => array(
				'status' => Dictionary::STATUS_SAVED, 
				'translation' => array(
					0 => "Test",
				),
			),
			'Test plural %s' => array(
				'status' => Dictionary::STATUS_SAVED,
				'translation' => array(
					0 => "Test plural %s 1",
					1 => "Test plural %s 2",
					2 => "Test plural %s 3",
				),
			),
		), $dictionary->iterator->getArrayCopy(), "dictionary");
	}

	public function testSave()
	{
		$this->markTestSkipped("Not implemented");
	}
}