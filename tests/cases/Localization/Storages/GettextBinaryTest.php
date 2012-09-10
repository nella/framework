<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Localization\GettextBinary;

use Nella\Localization\Storages\GettextBinary;

class GettextBinaryTest extends \Nella\Testing\TestCase
{
	public function testInstance()
	{
		$storage = new \Nella\Localization\Storages\GettextBinary('');
		$this->assertInstanceOf(
			'Nella\Localization\IStorage', $storage, "is instance of 'Nella\\Localization\\IStorage'"
		);
	}

	public function testLoad()
	{
		$file = $this->getContext()->expand('%fixturesDir%/%%lang%%.mo');
		$storage = new GettextBinary('', $file);
		$dictionary = $storage->load('cs_CZ');

		$this->assertEquals(array(
			'Plural-Forms' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4 ? 1 : 2);',
			'Project-Id-Version' => 'Čeština pro Nellu',
			'Report-Msgid-Bugs-To' => 'bugs@nellacms.com',
			'POT-Creation-Date' => '2009-07-01 11:07+0100',
			'PO-Revision-Date' => '2011-02-07 23:40+0100',
			'Last-Translator' => 'Patrik Votoček <patrik@votocek.cz>',
			'MIME-Version' => '1.0',
			'Content-Type' => 'text/plain; charset=UTF-8',
			'Content-Transfer-Encoding' => '8bit',
			'X-Poedit-Language' => 'Czech',
			'X-Poedit-Country' => 'CZECH REPUBLIC',
			'X-Poedit-SourceCharset' => 'utf-8',
			'X-Poedit-KeywordsList' => '__;_n;_x;_nx;_;!_',
			'Language-Team:' => NULL,
		), $dictionary->metadata, 'metadata');

		$this->assertEquals(array(
			'Test' => array('Test'),
			'Test plural %s' => array(
				'Test plural %s 1',
				'Test plural %s 2',
				'Test plural %s 3',
			),
		), iterator_to_array($dictionary), 'dictionary');
	}

	/**
	 * @depends testLoad
	 */
	public function testSave()
	{
		$file = $this->getContext()->expand('%tempDir%/%%lang%%.mo');
		$storage = new GettextBinary('', $file);

		$metadata = array('POT-Creation-Date' => date_create()->format('Y-m-d H:iO'));

		$dictionary = new \Nella\Localization\Dictionary('test');
		$dictionary->metadata = $metadata;
		$dictionary->addTranslation('simple', array('test'));
		$dictionary->addTranslation('plural', array('Lorem', 'Ipsum', 'Dolor'));

		$storage->save($dictionary);

		$dictionary = NULL;
		$dictionary = $storage->load('test');

		$this->assertEquals($metadata['POT-Creation-Date'], $dictionary->metadata['POT-Creation-Date']);

		$this->assertEquals(array(
			'simple' => array('test'),
			'plural' => array('Lorem', 'Ipsum', 'Dolor'),
		), iterator_to_array($dictionary), 'dictionary');
	}
}