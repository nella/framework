<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Security;

require_once __DIR__ . "/../bootstrap.php";

class IdentityDocumentTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Nella\Security\IdentityDocument */
	private $identity;
	
	public function setUp()
	{
		$this->identity = new \Nella\Security\IdentityDocument;	
	}
	
	public function testPassword()
	{
		$this->assertNull($this->identity->getPassword(), "->getPassword() is default NULL");
		
		$this->identity->setPassword("foo", "md5");
		$pass = $this->identity->getPassword(FALSE);
		$this->assertEquals("md5", $pass['algo'], "set password foo width MD5 algo");
		$this->assertEquals(md5($pass['salt'] . "foo"), $pass['hash'], "set password foo width MD5 hash");
		$this->assertTrue($this->identity->verifyPassword("foo"), "->verifyPassword() is TRUE via 'foo'");
		
		$this->identity->password = "bar";
		$this->assertTrue($this->identity->verifyPassword("bar"), "->verifyPassword() is TRUE via 'bar' - set via setter");
	}
}