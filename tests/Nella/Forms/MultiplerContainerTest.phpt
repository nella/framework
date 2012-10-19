<?php
/**
 * Test: Nella\Forms\MultiplerContainer
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Forms\MultiplerContainerTest
 */

namespace Nella\Tests\Forms;

use Assert,
	Nella\Forms\MultiplerContainer;

require_once __DIR__ . '/../../bootstrap.php';

class MultiplerContainerTest extends \TestCase
{
	/** @var \Nella\Forms\MultiplerContainer */
	private $container;

	public function setUp()
	{
		parent::setUp();
		$this->container = new MultiplerContainer;
	}

	public function testInstance()
	{
		Assert::true($this->container instanceof \Nette\Forms\Container, 'is Nette\Forms\Container instance');
		Assert::true($this->container instanceof \Nella\Forms\Container, 'is Nella\Forms\Container instance');
	}

	public function testAddRemoveContainerButton()
	{
		$this->container->addRemoveContainerButton("Remove container");

		Assert::true(isset($this->container[MultiplerContainer::REMOVE_CONTAINER_BUTTON_ID]), "is button exist");
		Assert::true(
			$this->container[MultiplerContainer::REMOVE_CONTAINER_BUTTON_ID] instanceof \Nette\Forms\Controls\SubmitButton,
			"is button valid type"
		);
	}
}
