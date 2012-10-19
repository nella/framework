<?php
/**
 * Test: Nella\Media\Doctrine\ImageFormatEntity
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Media\Doctrine\ImageFormatEntityTest
 */

namespace Nella\Tests\Media\Doctrine;

use Assert,
	Nella\Media\Doctrine\ImageFormatEntity;

require_once __DIR__ . '/../../../bootstrap.php';

class ImageFormatEntityTest extends \TestCase
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
		Assert::true($this->format instanceof \Nella\Media\IImageFormat, "instance IImageFormat");
	}

	public function testDefaultValuesSettersAndGetters()
	{
		Assert::null($this->format->getId(), "->getId() default value");
		Assert::null($this->format->getWidth(), "->getWidth() default value");
		Assert::null($this->format->getHeight(), "->getHeight() default value");
		Assert::null($this->format->getSlug(), "->getSlug() default value");
		Assert::null($this->format->getWatermark(), "->getWatermark() default value");
		Assert::null($this->format->getWatermarkPosition(), "->getWatermarkPosition() default value");
		Assert::null($this->format->getWatermarkOpacity(), "->getWatermarkOpacity() default value");
		Assert::equal(0, $this->format->getFlags(), "->getFlags default value");
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
		Assert::equal($value, $this->format->$getter(),
			"->$getter() equals " . (is_object($value) ? get_class($value) : $value)
		);
	}

	/**
	 * @dataProvider dataSettersAndGetters
	 */
	public function testSettersAndGettersProperties($property, $value)
	{
		$this->format->$property = $value;
		Assert::equal($value, $this->format->$property,
			"->$property equals " . (is_object($value) ? get_class($value) : $value)
		);
	}
}
