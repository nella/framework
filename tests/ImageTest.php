<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests;

use Nella\Image;

class ImageTest extends \Nella\Testing\TestCase
{
	public function testTypeToExt()
	{
		$this->assertEquals('jpg', Image::typeToExt(Image::JPEG), "::typeToExt(Image::JPEG)");
		$this->assertEquals('png', Image::typeToExt(Image::PNG), "::typeToExt(Image::PNG)");
		$this->assertEquals('gif', Image::typeToExt(Image::GIF), "::typeToExt(Image::GIF)");
	}

	/**
	 * @expectedException Nette\ArgumentOutOfRangeException
	 */
	public function testTypeToExtInvalidType()
	{
		Image::typeToExt(999999);
	}

	public function testExtToType()
	{
		$this->assertEquals(Image::JPEG, Image::extToType("jpg"), "::extToType('jpg')");
		$this->assertEquals(Image::JPEG, Image::extToType("jpeg"), "::extToType('jpeg')");
		$this->assertEquals(Image::PNG, Image::extToType("png"), "::extToType('png')");
		$this->assertEquals(Image::GIF, Image::extToType("gif"), "::extToType('gif')");
	}

	/**
	 * @expectedException Nette\ArgumentOutOfRangeException
	 */
	public function testExtToTypeInvalidType()
	{
		Image::extToType("foo");
	}

	/**
	 * @return array
	 */
	public function dataResizeAndCrop()
	{
		return array(
			array(200, 200, 100, 100, 100, 100),
			array(400, 100, 100, 100, 100, 100),
			array(100, 50, 100, 100, 100, 100),
			array(50, 100, 100, 100, 100, 100),
			array(50, 50, 100, 100, 100, 100),
		);
	}

	/**
	 * @dataProvider dataResizeAndCrop
	 */
	public function testResizeAndCrop($actWidth, $actHeight, $maxWidth, $maxHeight, $eqWidth, $eqHeight)
	{
		$img = Image::fromBlank($actWidth, $actHeight);
		$img->resizeAndCrop($maxWidth, $maxHeight);

		$this->assertEquals(
			array('width' => $eqWidth, 'height' => $eqHeight),
			array('width' => $img->width, 'height' => $img->height),
			"::resizeAndCrop($maxWidth, $maxHeight) from ($actWidth, $actHeight)"
		);
	}
}