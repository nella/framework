<?php
/**
 * Test: Nella\Event\Args\ApplicationError
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase
 */

namespace Nella\Tests\Event\Args;

use Tester\Assert,
	Nette\Application\Application;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Application/Application.php';

class ApplicationErrorTest extends \Tester\TestCase
{
	/** @var \Nella\Event\Args\ApplicationError */
	private $args;

	public function setUp()
	{
		parent::setUp();
		$application = new \Nella\Mocks\Application\Application;

		$this->args = new \Nella\Event\Args\ApplicationError($application, new \Exception);
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

	public function testGetException()
	{
		Assert::true($this->args->getException() instanceof \Exception, "getException()");
		Assert::true($this->args->exception instanceof \Exception, "->exception");
	}
}

id(new ApplicationErrorTest)->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
