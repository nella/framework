<?php
/**
 * Test: Nella\Media\Model\ImageFacade
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Media\Model\ImageFacadeTest
 */

namespace Nella\Tests\Media\Model;

use Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class ImageFacadeTest extends \TestCase
{
	const SLUG = 'logo_png';
	/** @var \Nella\Media\Model\ImageFacade */
	private $model;

	public function setUp()
	{
		parent::setUp();
		$this->model = new \Nella\Media\Model\ImageFacade;
	}

	public function testInstanceOf()
	{
		Assert::true($this->model instanceof \Nella\Media\Model\IImageDao);
	}

	public function testFindOneByFullSlug()
	{
		$image = $this->model->findOneByFullSlug(self::SLUG);
		Assert::true($image instanceof \Nella\Media\IImage);
		Assert::equal('logo', $image->getFullSlug());
	}
}
