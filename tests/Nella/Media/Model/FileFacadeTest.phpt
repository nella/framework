<?php
/**
 * Test: Nella\Media\Model\FileFacade
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Media\Model\FileFacadeTest
 */

namespace Nella\Tests\Media\Model;

use Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class FileFacadeTest extends \TestCase
{
	const SLUG = 'logo_png';
	/** @var \Nella\Media\Model\FileFacade */
	private $model;

	public function setUp()
	{
		parent::setUp();
		$this->model = new \Nella\Media\Model\FileFacade;
	}

	public function testInstanceOf()
	{
		Assert::true($this->model instanceof \Nella\Media\Model\IFileDao);
	}

	public function testFindOneByFullSlug()
	{
		$file = $this->model->findOneByFullSlug(self::SLUG);
		Assert::true($file instanceof \Nella\Media\IFile);
		Assert::equal('logo', $file->getFullSlug());
	}
}
