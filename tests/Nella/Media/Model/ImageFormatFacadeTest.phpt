<?php
/**
 * Test: Nella\Media\Model\ImageFormatFacade
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Media\Model;

use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class ImageFormatFacadeTest extends \Tester\TestCase
{
	const SLUG = 'default';
	/** @var \Nella\Media\Model\ImageFormatFacade */
	private $model;

	public function setUp()
	{
		parent::setUp();
		$this->model = new \Nella\Media\Model\ImageFormatFacade(array(
			self::SLUG => array(
				'width' => 800,
				'height' => 600,
			)
		));
	}

	public function testInstanceOf()
	{
		Assert::true($this->model instanceof \Nella\Media\Model\IImageFormatDao);
	}

	public function testFindOneByFullSlug()
	{
		$image = $this->model->findOneByFullSlug(self::SLUG);
		Assert::true($image instanceof \Nella\Media\IImageFormat);
		Assert::equal(self::SLUG, $image->getFullSlug(), '->getFullSlug()');
		Assert::equal(800, $image->getWidth(), '->getWidth()');
		Assert::equal(600, $image->getHeight(), '->getHeight()');
		Assert::equal(4, $image->getFlags(), '->getFlags()');
		Assert::false($image->isCrop(), '->isCrop()');

	}
}

id(new ImageFormatFacadeTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
