<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Media\Callbacks;

class ImagePresenterCallbackTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Media\Callbacks\FilePresenterCallback */
	private $callback;
	/** @var string */
	private $dir;

	public function setup()
	{
		$this->dir = $this->getContext()->parameters['tempDir'];
		$storage = new \Nella\Media\Storages\File($this->getContext()->parameters['fixturesDir']);
		$cache = new \Nella\Media\ImageCacheStorages\File($this->dir);
		$this->callback = new \Nella\Media\Callbacks\ImagePresenterCallback($storage, $cache);
	}

	public function testInstanceOf()
	{
		$this->assertInstanceOf('Nella\Media\IImagePresenterCallback', $this->callback, 'is Nella\Media\IImagePresenterCallback');
	}

	public function testInvoke()
	{
		$image = $this->getMock('Nella\Media\IImage');
		$image->expects($this->any())->method('getPath')->will($this->returnValue('logo.png'));
		$image->expects($this->any())->method('getFullSlug')->will($this->returnValue('test-logo'));
		$format = $this->getMock('Nella\Media\IImageFormat');
		$format->expects($this->any())->method('getFullSlug')->will($this->returnValue('thumbnail'));
		$format->expects($this->any())->method('getWidth')->will($this->returnValue(100));
		$format->expects($this->any())->method('getHeight')->will($this->returnValue(100));
		$format->expects($this->any())->method('getWatermark')->will($this->returnValue(NULL));

		$response = callback($this->callback)->invoke($image, $format, "png");
		$this->assertInstanceOf('Nella\Media\Responses\ImageResponse', $response, 'instance ImageResponse');
	}

	/**
	 * @expectedException Nette\Application\BadRequestException
	 */
	public function testInvokeInvalidFile()
	{
		$image = $this->getMock('Nella\Media\IImage');
		$image->expects($this->any())->method('getPath')->will($this->returnValue('invalid.png'));
		$format = $this->getMock('Nella\Media\IImageFormat');

		callback($this->callback)->invoke($image, $format, "png");
	}
}
