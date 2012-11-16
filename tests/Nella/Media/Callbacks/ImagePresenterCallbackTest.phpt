<?php
/**
 * Test: Nella\Media\Callbacks\ImagePresenterCallback
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Media\Callbacks;

use Tester\Assert,
	Nella\Mocks\Media\Image,
	Nella\Mocks\Media\Format;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Media/Image.php';
require_once MOCKS_DIR . '/Media/Format.php';

class ImagePresenterCallbackTest extends \Tester\TestCase
{
	/** @var \Nella\Media\Callbacks\FilePresenterCallback */
	private $callback;
	/** @var string */
	private $dir;

	public function setup()
	{
		$this->dir = TEMP_DIR;
		$storage = new \Nella\Media\Storages\File(FIXTURES_DIR);
		$cache = new \Nella\Media\ImageCacheStorages\File($this->dir);
		$this->callback = new \Nella\Media\Callbacks\ImagePresenterCallback($storage, $cache);
	}

	public function testInstanceOf()
	{
		Assert::true($this->callback instanceof \Nella\Media\IImagePresenterCallback, 'is Nella\Media\IImagePresenterCallback');
	}

	public function testInvoke()
	{
		$image = new Image;
		$image->path = 'logo.png';
		$image->slug = 'test-logo';
		$format = new Format;
		$format->slug = 'thumbnail';

		$response = callback($this->callback)->invoke($image, $format, "png");
		Assert::true($response instanceof \Nella\Media\Responses\ImageResponse, 'instance ImageResponse');
	}

	public function testInvokeInvalidFile()
	{
		$callback = $this->callback;
		Assert::throws(function() use($callback) {
			$image = new Image;
			$image->path = 'invalid.php';
			$format = new Format;
			callback($callback)->invoke($image, $format, "png");
		}, 'Nette\Application\BadRequestException');
	}
}

id(new ImagePresenterCallbackTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
