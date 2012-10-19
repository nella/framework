<?php
/**
 * Test: Nella\Event\Args\Container
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Event\Args\ContainerTest
 */

namespace Nella\Tests\Event\Args;

use Assert,
	Nette\DI\Container;

require_once __DIR__ . '/../../../bootstrap.php';

class ContainerTest extends \TestCase
{
	/** @var \Nella\Event\Args\Container */
	private $args;

	public function setUp()
	{
		parent::setUp();
		$this->args = new \Nella\Event\Args\Container(new Container);
	}

	public function testInstance()
	{
		Assert::true($this->args instanceof \Nella\Event\EventArgs);
	}

	public function testGetApplication()
	{
		Assert::true($this->args->getContainer() instanceof Container, "->getApplication()");
		Assert::true($this->args->container instanceof Container, "->application");
	}
}
