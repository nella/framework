<?php
/**
 * Test: Nella\Media\Doctrine\ImageEntity
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Media\Doctrine\ImageEntityTest
 */

namespace Nella\Tests\Media\Doctrine;

use Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class ImageEntityTest extends \TestCase
{
	/** @var \Nella\Media\Doctrine\ImageEntity */
	private $image;

	public function setUp()
	{
		parent::setUp();
		$this->image = new \Nella\Media\Doctrine\ImageEntity('foo.bar', 'image/png');
	}

	public function testInstance()
	{
		Assert::true($this->image instanceof \Nella\Media\IImage, "instance IImage");
	}

	public function testDefaultValuesSettersAndGetters()
	{
		Assert::null($this->image->getId(), "->getId() default value");
		Assert::equal('foo.bar', $this->image->getPath(), "->getPath() default value");
		Assert::null($this->image->getSlug(FALSE), "->getSlug(FALSE) default value");
		Assert::equal('image/png', $this->image->getContentType(), "->getContentType() default value");
		Assert::equal('png', $this->image->getImageType(), "->getImageType() default value");
	}

	public function dataSettersAndGetters()
	{
		return array(
			array('slug', "logo"),
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersMethods($method, $value)
	{
		$setter = "set" . ucfirst($method);
		$getter = "get" . ucfirst($method);
		$this->image->$setter($value);
		Assert::equal($value, $this->image->$getter(),
			"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->image->$property = $value;
		Assert::equal($value, $this->image->$property,
			"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}
}
