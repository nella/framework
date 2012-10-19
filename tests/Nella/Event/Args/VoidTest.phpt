<?php
/**
 * Test: Nella\Event\Args\Void
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Event\Args\VoidTest
 */

namespace Nella\Tests\Event\Args;

use Assert;

require_once __DIR__ . '/../../../bootstrap.php';

class VoidTest extends \TestCase
{
	/** @var \Nella\Event\Args\Void */
	private $args;

	public function setUp()
	{
		parent::setUp();

		$this->args = new \Nella\Event\Args\Void;
	}

	public function testInstance()
	{
		Assert::true($this->args instanceof \Nella\Event\EventArgs);
	}
}
