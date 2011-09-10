<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\DI;

class ContainerHelperTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\DI\ContainerHelper */
	private $helper;

	public function setup()
	{
		parent::setup();
		$this->helper = new \Nella\DI\ContainerHelper($this->context);
	}

	public function testGetContainer()
	{
		$this->assertInstanceOf(
			'Nette\DI\Container',
			$this->helper->getContainer(),
			'->getContainer() instance Nette\\DI\\Container'
		);
		$this->assertSame($this->context, $this->helper->getContainer());
	}

	public function testGetName()
	{
		$this->assertEquals('diContainer', $this->helper->getName());
	}
}