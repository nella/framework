<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\NetteAddons\Forms;

use Nella\NetteAddons\Forms\MultiplerContainer;

class MultiplerContainerTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\NetteAddons\Forms\MultiplerContainer */
	private $container;

	public function setup()
	{
		$this->container = new MultiplerContainer;
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nette\Forms\Container', $this->container, 'is Nette\Forms\Container instance');
		$this->assertInstanceOf('Nella\NetteAddons\Forms\Container', $this->container, 'is Nella\NetteAddons\Forms\Container instance');
	}

	public function testAddRemoveContainerButton()
	{
		$this->container->addRemoveContainerButton("Remove container");

		$this->assertTrue(isset($this->container[MultiplerContainer::REMOVE_CONTAINER_BUTTON_ID]), "is button exist");
		$this->assertInstanceOf(
			'Nette\Forms\Controls\SubmitButton', $this->container[MultiplerContainer::REMOVE_CONTAINER_BUTTON_ID],
			"is button valid type"
		);
	}
}