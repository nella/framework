<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Media\Doctrine;

use Nella\Media\Doctrine\ImageFormatEntity;

class ImageFormatEntityTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Media\Doctrine\ImageFormatEntity */
	private $format;

	public function setup()
	{
		parent::setup();
		$this->format = new ImageFormatEntity;
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Media\IImageFormat', $this->format, "instance IImageFormat");
	}

	public function testDefaultValuesSettersAndGetters()
	{
		$this->assertNull($this->format->getId(), "->getId() default value");
		$this->assertNull($this->format->getWidth(), "->getWidth() default value");
		$this->assertNull($this->format->getHeight(), "->getHeight() default value");
		$this->assertNull($this->format->getSlug(), "->getSlug() default value");
		$this->assertNull($this->format->getWatermark(), "->getWatermark() default value");
		$this->assertNull($this->format->getWatermarkPosition(), "->getWatermarkPosition() default value");
		$this->assertNull($this->format->getWatermarkOpacity(), "->getWatermarkOpacity() default value");
		$this->assertEquals(0, $this->format->getFlags(), "->getFlags default value");
	}

	public function dataSettersAndGetters()
	{
		return array(
			array('width', 100),
			array('height', 200),
			array('flags', \Nette\Image::FILL),
			array('slug', "format"),
			array('watermark', new \Nella\Media\Doctrine\ImageEntity('foo.bar', 'image/gif')),
			array('watermarkPosition', ImageFormatEntity::POSITION_CENTER),
			array('watermarkOpacity', 20),
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersMethods($method, $value)
	{
		$setter = "set" . ucfirst($method);
		$getter = "get" . ucfirst($method);
		$this->format->$setter($value);
		$this->assertEquals($value, $this->format->$getter(),
			"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->format->$property = $value;
		$this->assertEquals($value, $this->format->$property,
			"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}
}