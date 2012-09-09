<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Media\Model;

class ImageDaoTest extends \Nella\Testing\TestCase
{
	const SLUG = 'logo_png';
	/** @var \Nella\Media\Model\ImageDao */
	private $model;

	public function setup()
	{
		$this->model = new \Nella\Media\Model\ImageDao;
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\Media\Model\IImageDao', $this->model);
	}

	public function testFindOneByFullSlug()
	{
		$image = $this->model->findOneByFullSlug(self::SLUG);
		$this->assertInstanceOf('Nella\Media\IImage', $image);
		$this->assertEquals('logo', $image->getFullSlug());
	}
}
