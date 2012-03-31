<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\NetteAddons\Media\Model;

class FileDaoTest extends \Nella\Testing\TestCase
{
	const SLUG = 'logo_png';
	/** @var \Nella\NetteAddons\Media\Model\FileDao */
	private $model;

	public function setup()
	{
		$this->model = new \Nella\NetteAddons\Media\Model\FileDao;
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\Model\IFileDao', $this->model);
	}

	public function testFindOneByFullSlug()
	{
		$file = $this->model->findOneByFullSlug(self::SLUG);
		$this->assertInstanceOf('Nella\NetteAddons\Media\IFile', $file);
		$this->assertEquals('logo', $file->getFullSlug());
	}
}
