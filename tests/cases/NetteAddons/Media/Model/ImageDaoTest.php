<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\NetteAddons\Media\Model;

class ImageDaoTest extends \Nella\Testing\TestCase
{
	const SLUG = 'logo_png';
	/** @var \Nella\NetteAddons\Media\Model\ImageDao */
	private $model;

	public function setup()
	{
		$this->model = new \Nella\NetteAddons\Media\Model\ImageDao;
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\Model\IImageDao', $this->model);
	}

	public function testFindOneByFullSlug()
	{
		$image = $this->model->findOneByFullSlug(self::SLUG);
		$this->assertInstanceOf('Nella\NetteAddons\Media\IImage', $image);
		$this->assertEquals('logo', $image->getFullSlug());
	}
}
