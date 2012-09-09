<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Media\Model;

class FileDaoTest extends \Nella\Testing\TestCase
{
	const SLUG = 'logo_png';
	/** @var \Nella\Media\Model\FileDao */
	private $model;

	public function setup()
	{
		$this->model = new \Nella\Media\Model\FileDao;
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\Media\Model\IFileDao', $this->model);
	}

	public function testFindOneByFullSlug()
	{
		$file = $this->model->findOneByFullSlug(self::SLUG);
		$this->assertInstanceOf('Nella\Media\IFile', $file);
		$this->assertEquals('logo', $file->getFullSlug());
	}
}
