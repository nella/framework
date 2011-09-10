<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Validator\MetadataParsers;

class AnnotationTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Validator\MetadataParsers\Annotation */
	private $parser;

	public function setup()
	{
		$this->parser = new \Nella\Validator\MetadataParsers\Annotation;
	}

	public function testParse()
	{
		$metadata = new \Nella\Validator\ClassMetadata('NellaTests\Validator\Annotation\Foo');
		$this->parser->parse($metadata);
		$this->assertEquals(2, count($metadata->rules['foo']), "parsed true rules");
	}
}

namespace NellaTests\Validator\Annotation;

class Foo
{
	/**
	 * @validate(url,minlength=20)
	 * @var mixed
	 */
	private $foo;
}
