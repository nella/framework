<?php
/**
 * Test: Nella\Security\Model\IdentityEntity
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Security\Model;

use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class IdentityEntityTest extends \Tester\TestCase
{
	/** @var \Nella\Security\Model\IdentityEntity */
	private $identity;

	public function setUp()
	{
		parent::setUp();
		$this->identity = new \Nella\Security\Model\IdentityEntity;
	}

	public function testInstance()
	{
		Assert::true($this->identity instanceof \Nette\Security\IIdentity);
	}

	public function testDefaultValuesSettersAndGetters()
	{
		Assert::null($this->identity->getId(), "->getId() default value");
		Assert::null($this->identity->getDisplayName(), "->getDisplayName() default value");
		Assert::equal(array(), $this->identity->getRoles(), "->getRoles() default value");
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
		Assert::equal($value, $this->identity->$getter(),
			"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->identity->$property = $value;
		Assert::equal($value, $this->identity->$property,
			"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}
}

id(new IdentityEntityTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
