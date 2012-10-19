<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Event\Args;

class VoidTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Event\Args\Void */
	private $args;

	public function setup()
	{
		parent::setup();
		$this->args = new \Nella\Event\Args\Void;
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Event\EventArgs', $this->args);
	}
}