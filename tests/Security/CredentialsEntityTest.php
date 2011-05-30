<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Security;

require_once __DIR__ . "/../bootstrap.php";

class CredentialsEntityTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Security\CredentialsEntity */
	private $credentials;

	public function setup()
	{
		$this->credentials = new \Nella\Security\CredentialsEntity;
	}

	public function testPassword()
	{
		$this->assertNull($this->credentials->getPassword(), "->getPassword() is default NULL");

		$this->credentials->setPassword("foo", "md5");
		$pass = $this->credentials->getPassword(FALSE);
		$this->assertEquals("md5", $pass[0], "set password foo width MD5 algo");
		$this->assertEquals(md5($pass[1] . "foo"), $pass[2], "set password foo width MD5 hash");
		$this->assertTrue($this->credentials->verifyPassword("foo"), "->verifyPassword() is TRUE via 'foo'");

		$this->credentials->password = "bar";
		$this->assertTrue($this->credentials->verifyPassword("bar"), "->verifyPassword() is TRUE via 'bar' - set via setter");
	}
}