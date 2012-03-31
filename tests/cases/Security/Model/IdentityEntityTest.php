<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Security\Model;

class IdentityEntityTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Security\Model\IdentityEntity */
	private $identity;

	public function setup()
	{
		parent::setup();
		$this->identity = new \Nella\Security\Model\IdentityEntity;
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nette\Security\IIdentity', $this->identity);
	}

	public function testDefaultValuesSettersAndGetters()
	{
		$this->assertNull($this->identity->getId(), "->getId() default value");
		$this->assertNull($this->identity->getDisplayName(), "->getDisplayName() default value");
		$this->assertEquals(array(), $this->identity->getRoles(), "->getRoles() default value");
	}

	public function dataSettersAndGetters()
	{
		return array(
			array('displayName', 'Vrtak-CZ'),
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersMethods($method, $value)
	{
		$setter = "set" . ucfirst($method);
		$getter = "get" . ucfirst($method);
		$this->identity->$setter($value);
		$this->assertEquals($value, $this->identity->$getter(),
				"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->identity->$property = $value;
		$this->assertEquals($value, $this->identity->$property,
				"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}
}
