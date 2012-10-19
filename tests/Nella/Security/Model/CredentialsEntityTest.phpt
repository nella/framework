<?php
/**
 * Test: Nella\Security\Model\CredentialsEntity
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Security\Model\CredentialsEntityTest
 */

namespace Nella\Tests\Security\Model;

use Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class CredentialsEntityTest extends \TestCase
{
	/** @var \Nella\Security\Model\IdentityEntity */
	private $identity;
	/** @var \Nella\Security\Model\CredentialsEntity */
	private $credentials;

	public function setUp()
	{
		parent::setUp();
		$this->identity = new \Nella\Security\Model\IdentityEntity;
		$this->credentials = new \Nella\Security\Model\CredentialsEntity($this->identity);
	}

	public function testDefaultValuesSettersAndGetters()
	{
		Assert::null($this->credentials->getId(), "->getId() default value");
		Assert::null($this->credentials->getUsername(), "->getUsername() default value");
		Assert::null($this->credentials->getPassword(), "->getPassword() default value");
		Assert::null($this->credentials->getEmail(), "->getEmail() default value");
		Assert::same($this->identity, $this->credentials->getIdentity(), "->getIdentity() default value");
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
		Assert::equal($value, $this->credentials->$getter(),
			"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->credentials->$property = $value;
		Assert::equal($value, $this->credentials->$property,
			"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	public function testPasswordSetterAndGetter()
	{
		$this->credentials->password = "loremIpsum";
		$data = $this->credentials->getPassword(FALSE);
		Assert::equal('sha256', $data[0], 'algo');
		Assert::equal(hash($data[0], $data[1]."loremIpsum"), $data[2], 'hash');

	}

	public function testVerifyPassword()
	{
		$this->credentials->password = "loremIpsum";
		Assert::true($this->credentials->verifyPassword("loremIpsum"));
		Assert::false($this->credentials->verifyPassword("fail"));
	}
}
