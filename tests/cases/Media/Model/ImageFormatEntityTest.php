<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Media\Model;

use Nella\Media\Model\ImageFormatEntity;

class ImageFormatEntityTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Media\Model\ImageFormatEntity */
	private $format;

	public function setup()
	{
		parent::setup();
		$this->format = new ImageFormatEntity;
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\IImageFormat', $this->format, "instance IImageFormat");
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
			array('watermark', new \Nella\Media\Model\ImageEntity('foo.bar', 'image/gif')), 
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