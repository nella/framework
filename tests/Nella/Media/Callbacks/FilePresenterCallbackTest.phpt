<?php
/**
 * Test: Nella\Media\Callbacks\FilePresenterCallback
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
	Nella\Mocks\Media\File;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Media/File.php';

class FilePresenterCallbackTest extends \Tester\TestCase
{
	/** @var \Nella\Media\Callbacks\FilePresenterCallback */
	private $callback;

	public function setup()
	{
		$storage = new \Nella\Media\Storages\File(FIXTURES_DIR);
		$this->callback = new \Nella\Media\Callbacks\FilePresenterCallback($storage);
	}

	public function testInstanceOf()
	{
		Assert::true($this->callback instanceof \Nella\Media\IFilePresenterCallback, 'is Nella\Media\IFilePresenterCallback');
	}

	public function testInvoke()
	{
		$file = new File;
		$file->path = 'logo.png';

		$response = callback($this->callback)->invoke($file);
		Assert::true($response instanceof \Nette\Application\Responses\FileResponse, 'instance FileResponse');
		Assert::equal('logo.png', $response->getName(), "file name 'logo.png'");
		Assert::equal('image/png', $response->getContentType(), "file content-type 'image/png'");
	}

	public function testInvokeInvalidFile()
	{
		$callback = $this->callback;
		Assert::throws(function() use($callback) {
			$file = new File;
			$file->path = 'tmp-logo.png';

			callback($callback)->invoke($file);
		}, 'Nette\Application\BadRequestException');
	}
}

id(new FilePresenterCallbackTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
