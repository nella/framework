<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Localization;

class GettextParserTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Localization\GettextParser */
	private $parser;
	
	public function setUp()
	{
		$this->parser = new \Nella\Localization\GettextParser;
	}
	
	public function testDecode()
	{
		$data = $this->parser->decode(__DIR__ . "/GettextParserTest.mo");
		
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
		), $data['metadata'], "metadata");
		
		$this->assertEquals(array(
			'Test' => array(
				'original' => array(
					0 => "Test", 
				), 
				'translation' => array(
					0 => "Test", 
				), 
			), 
			'Test plural %s' => array(
				'original' => array(
					0 => "Test plural %s", 
					1 => "Test plurals %s", 
				), 
				'translation' => array(
					0 => "Test plural %s 1", 
					1 => "Test plural %s 2", 
					2 => "Test plural %s 3", 
				), 
			), 
		), $data['dictionary'], "dictionary");
	}
	
	public function testEncode()
	{
		$this->markTestSkipped("Not implemented");
	}
}