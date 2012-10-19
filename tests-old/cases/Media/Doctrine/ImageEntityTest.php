<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Media\Doctrine;

class ImageEntityTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Media\Doctrine\ImageEntity */
	private $image;

	public function setup()
	{
		parent::setup();
		$this->image = new \Nella\Media\Doctrine\ImageEntity('foo.bar', 'image/png');
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Media\IImage', $this->image, "instance IImage");
	}

	public function testDefaultValuesSettersAndGetters()
	{
		$this->assertNull($this->image->getId(), "->getId() default value");
		$this->assertEquals('foo.bar', $this->image->getPath(), "->getPath() default value");
		$this->assertNull($this->image->getSlug(FALSE), "->getSlug(FALSE) default value");
		$this->assertEquals('image/png', $this->image->getContentType(), "->getContentType() default value");
		$this->assertEquals('png', $this->image->getImageType(), "->getImageType() default value");
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
		$this->assertEquals($value, $this->image->$getter(),
			"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->image->$property = $value;
		$this->assertEquals($value, $this->image->$property,
			"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}
}