<?php
/**
 * Test: Nella\Localization\Storages\GettextBinary
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Localization\Storages;

use Tester\Assert,
	Nella\Localization\Storages\GettextBinary;

require_once __DIR__ . '/../../../bootstrap.php';

class GettextBinaryTest extends \Tester\TestCase
{
	public function testInstance()
	{
		$storage = new GettextBinary('');
		Assert::true(
			$storage instanceof \Nella\Localization\IStorage, "is instance of 'Nella\\Localization\\IStorage'"
		);
	}

	public function testLoad()
	{
		$file = FIXTURES_DIR . '/%lang%.mo';
		$storage = new GettextBinary('', $file);
		$dictionary = $storage->load('cs_CZ');

		Assert::equal(array(
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

		Assert::equal(array(
			'Test' => array('Test'),
			'Test plural %s' => array(
				'Test plural %s 1',
				'Test plural %s 2',
				'Test plural %s 3',
			),
		), iterator_to_array($dictionary), 'dictionary');
	}

	public function testSave()
	{
		$file = TEMP_DIR . '/%lang%.mo';
		$storage = new GettextBinary('', $file);

		$metadata = array('POT-Creation-Date' => date_create()->format('Y-m-d H:iO'));

		$dictionary = new \Nella\Localization\Dictionary('test');
		$dictionary->metadata = $metadata;
		$dictionary->addTranslation('simple', array('test'));
		$dictionary->addTranslation('plural', array('Lorem', 'Ipsum', 'Dolor'));

		$storage->save($dictionary);

		$dictionary = NULL;
		$dictionary = $storage->load('test');

		Assert::equal($metadata['POT-Creation-Date'], $dictionary->metadata['POT-Creation-Date']);

		Assert::equal(array(
			'simple' => array('test'),
			'plural' => array('Lorem', 'Ipsum', 'Dolor'),
		), iterator_to_array($dictionary), 'dictionary');
	}
}

id(new GettextBinaryTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
