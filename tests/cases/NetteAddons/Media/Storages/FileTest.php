<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\NetteAddons\Media\Storages;

class FileTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\NetteAddons\Media\Storages\File */
	private $storage;
	/** @var string */
	private $dir;

	public function setup()
	{
		$this->dir = $this->getContext()->parameters['tempDir'];
		$this->storage = new \Nella\NetteAddons\Media\Storages\File($this->dir);
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\IStorage', $this->storage, 'is Nella\NetteAddons\Media\IStorage');
	}

	public function dataTest()
	{
		$file = $this->getMock('Nella\NetteAddons\Media\IFile');
		$file->expects($this->any())->method('getPath')->will($this->returnValue('tmp-logo.png'));
		return array(array($file));
	}

	/**
	 * @dataProvider dataTest
	 */
	public function testSave($file)
	{
		$source = $this->getContext()->parameters['fixturesDir'] . '/logo.png';

		$this->storage->save($file, $source);

		$this->assertFileExists($this->dir . '/tmp-logo.png');
	}

	/**
	 * @depends testSave
	 * @dataProvider dataTest
	 */
	public function testLoad($file)
	{
		$path = $this->storage->load($file);

		$this->assertEquals($this->dir . '/tmp-logo.png', $path);
	}

	/**
	 * @depends testLoad
	 * @dataProvider dataTest
	 */
	public function testRemove($file)
	{
		$this->storage->remove($file);

		$this->assertFileNotExists($this->dir . '/tmp-logo.png');
	}

	public function testLoadInvalidFile()
	{
		$file = $this->getMock('Nella\NetteAddons\Media\IFile');
		$file->expects($this->any())->method('getPath')->will($this->returnValue('tmp-logo-invalid.png'));

		$this->assertNull($this->storage->load($file));
	}
}
