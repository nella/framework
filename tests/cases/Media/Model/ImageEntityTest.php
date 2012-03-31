<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Media\Model;

class ImageEntityTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Media\Model\ImageEntity */
	private $image;

	public function setup()
	{
		parent::setup();
		$this->image = new \Nella\Media\Model\ImageEntity('foo.bar', 'image/png');
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\IImage', $this->image, "instance IImage");
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