<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Validator;

require_once __DIR__ . "/../bootstrap.php";

class AnnotationParserTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Validator\AnnotationParser */
	private $parser;
	
	public function setUp()
	{
		$this->parser = new \Nella\Validator\AnnotationParser;
	}
	
	public function testParse()
	{
		$metadata = new \Nella\Validator\ClassMetadata('NellaTests\Validator\Foo');
		$this->parser->parse($metadata);
		$this->assertEquals(2, count($metadata->rules['foo']), "parsed true rules");
	}
}
