<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\NetteAddons\Media\Callbacks;

class FilePresenterCallbackTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\NetteAddons\Media\Callbacks\FilePresenterCallback */
	private $callback;
	
	public function setup()
	{
		$storage = new \Nella\NetteAddons\Media\Storages\File($this->getContext()->parameters['fixturesDir']);
		$this->callback = new \Nella\NetteAddons\Media\Callbacks\FilePresenterCallback($storage);
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\NetteAddons\Media\IFilePresenterCallback', $this->callback, 'is Nella\NetteAddons\Media\IFilePresenterCallback');
	}

	public function testInvoke()
	{
		$file = $this->getMock('Nella\NetteAddons\Media\IFile');
		$file->expects($this->any())->method('getPath')->will($this->returnValue('logo.png'));
		$file->expects($this->any())->method('getContentType')->will($this->returnValue('image/png'));
		
		$response = callback($this->callback)->invoke($file);
		$this->assertInstanceOf('Nette\Application\Responses\FileResponse', $response, 'instance FileResponse');
		$this->assertEquals('logo.png', $response->getName(), "file name 'logo.png'");
		$this->assertEquals('image/png', $response->getContentType(), "file content-type 'image/png'");
	}
	
	/**
	 * @expectedException Nette\Application\BadRequestException
	 */
	public function testInvokeInvalidFile()
	{
		$file = $this->getMock('Nella\NetteAddons\Media\IFile');
		$file->expects($this->any())->method('getPath')->will($this->returnValue('tmp-logo.png'));
		
		callback($this->callback)->invoke($file);
	}
}
