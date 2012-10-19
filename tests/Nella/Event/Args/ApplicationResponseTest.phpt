<?php
/**
 * Test: Nella\Event\Args\ApplicationResponse
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Event\Args\ApplicationResponseTest
 */

namespace Nella\Tests\Event\Args;

use Assert,
	Nette\Application\Application,
	Nette\Application\IResponse;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Application/Application.php';

class ApplicationResponseTest extends \TestCase
{
	/** @var \Nella\Event\Args\ApplicationResponse */
	private $args;

	public function setUp()
	{
		parent::setUp();
		$application = new \Nella\Mocks\Application\Application;

		$response = new \Nette\Application\Responses\TextResponse('');

		$this->args = new \Nella\Event\Args\ApplicationResponse($application, $response);
	}

	public function testInstance()
	{
		Assert::true($this->args instanceof \Nella\Event\EventArgs);
	}

	public function testGetApplication()
	{
		Assert::true($this->args->getApplication() instanceof Application, "->getApplication()");
		Assert::true($this->args->application instanceof Application, "->application");
	}

	public function testGetResponse()
	{
		Assert::true($this->args->getResponse() instanceof IResponse, "->getResponse()");
		Assert::true($this->args->response instanceof IResponse, "->response");
	}
}
