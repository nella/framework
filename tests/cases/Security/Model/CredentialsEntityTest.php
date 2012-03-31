<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Security\Model;

class CredentialsEntityTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Security\Model\IdentityEntity */
	private $identity;
	/** @var \Nella\Security\Model\CredentialsEntity */
	private $credentials;

	public function setup()
	{
		parent::setup();
		$this->identity = new \Nella\Security\Model\IdentityEntity;
		$this->credentials = new \Nella\Security\Model\CredentialsEntity($this->identity);
	}

	public function testDefaultValuesSettersAndGetters()
	{
		$this->assertNull($this->credentials->getId(), "->getId() default value");
		$this->assertNull($this->credentials->getUsername(), "->getUsername() default value");
		$this->assertNull($this->credentials->getPassword(), "->getPassword() default value");
		$this->assertNull($this->credentials->getEmail(), "->getEmail() default value");
		$this->assertSame($this->identity, $this->credentials->getIdentity(), "->getIdentity() default value");
	}

	public function dataSettersAndGetters()
	{
		return array(
			array('username', 'Vrtak-CZ'),
			array('email', 'info@nella-project.org'),
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersMethods($method, $value)
	{
		$setter = "set" . ucfirst($method);
		$getter = "get" . ucfirst($method);
		$this->credentials->$setter($value);
		$this->assertEquals($value, $this->credentials->$getter(),
				"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->credentials->$property = $value;
		$this->assertEquals($value, $this->credentials->$property,
				"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	public function testPasswordSetterAndGetter()
	{
		$this->credentials->password = "loremIpsum";
		$data = $this->credentials->getPassword(FALSE);
		$this->assertEquals('sha256', $data[0], 'algo');
		$this->assertEquals(hash($data[0], $data[1]."loremIpsum"), $data[2], 'hash');

	}

	public function testVerifyPassword()
	{
		$this->credentials->password = "loremIpsum";
		$this->assertTrue($this->credentials->verifyPassword("loremIpsum"));
		$this->assertFalse($this->credentials->verifyPassword("fail"));
	}
}
