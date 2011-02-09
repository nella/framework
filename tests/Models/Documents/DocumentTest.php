<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Models;

require_once __DIR__ . "/../../bootstrap.php";

class DocumentTest extends \PHPUnit_Framework_TestCase
{
	/** @var DocumentMock */
	private $document;
	
	public function setUp()
	{
		$this->document = new DocumentMock;
	}
	
	public function testGetId()
	{
		$this->assertNull($this->document->getId(), "->getId() default is NULL");
		$this->assertNull($this->document->id, "->id default is NULL");
		
		$this->document = new DocumentMock(123);
		$this->assertEquals(123, $this->document->getId(), "->getId() is 123");
		$this->assertEquals(123, $this->document->id, "->id is 123");
	}
}
