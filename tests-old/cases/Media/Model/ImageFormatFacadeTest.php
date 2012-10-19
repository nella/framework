<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Media\Model;

class ImageFormatFacadeTest extends \Nella\Testing\TestCase
{
	const SLUG = 'default';
	/** @var \Nella\Media\Model\ImageFormatFacade */
	private $model;

	public function setup()
	{
		$this->model = new \Nella\Media\Model\ImageFormatFacade(array(
			self::SLUG => array(
				'width' => 800,
				'height' => 600,
			)
		));
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\Media\Model\IImageFormatDao', $this->model);
	}

	public function testFindOneByFullSlug()
	{
		$image = $this->model->findOneByFullSlug(self::SLUG);
		$this->assertInstanceOf('Nella\Media\IImageFormat', $image);
		$this->assertEquals(self::SLUG, $image->getFullSlug(), '->getFullSlug()');
		$this->assertEquals(800, $image->getWidth(), '->getWidth()');
		$this->assertEquals(600, $image->getHeight(), '->getHeight()');
		$this->assertEquals(4, $image->getFlags(), '->getFlags()');
		$this->assertFalse($image->isCrop(), '->isCrop()');

	}
}
