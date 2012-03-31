<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\NetteAddons\Media\Model;

class ImageFormatDaoTest extends \Nella\Testing\TestCase
{
	const SLUG = 'default';
	/** @var \Nella\NetteAddons\Media\Model\ImageFormatDao */
	private $model;

	public function setup()
	{
		$this->model = new \Nella\NetteAddons\Media\Model\ImageFormatDao(array(
			self::SLUG => array(
				'width' => 800,
				'height' => 600,
			)
		));
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\Model\IImageFormatDao', $this->model);
	}

	public function testFindOneByFullSlug()
	{
		$image = $this->model->findOneByFullSlug(self::SLUG);
		$this->assertInstanceOf('Nella\NetteAddons\Media\IImageFormat', $image);
		$this->assertEquals(self::SLUG, $image->getFullSlug(), '->getFullSlug()');
		$this->assertEquals(800, $image->getWidth(), '->getWidth()');
		$this->assertEquals(600, $image->getHeight(), '->getHeight()');
		$this->assertEquals(4, $image->getFlags(), '->getFlags()');
		$this->assertFalse($image->isCrop(), '->isCrop()');

	}
}
