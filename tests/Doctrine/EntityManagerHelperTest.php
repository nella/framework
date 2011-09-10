<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Doctrine;

require_once __DIR__ . "/../Mocks/EntityManagerMock.php";

class EntityManagerHelperTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Doctrine\Container */
	private $dc;
	/** @var \Nella\DI\ContainerHelper */
	private $helper;

	public function setup()
	{
		parent::setup();
		$container = new \Nette\DI\Container;
		$container->addService('entityManager', \Doctrine\Tests\Mocks\EntityManagerMock::create(
			new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver)
		));
		$this->dc = new \Nella\Doctrine\Container($container);
		$this->helper = new \Nella\Doctrine\EntityManagerHelper($this->dc);
	}

	public function testGetContainer()
	{
		$this->assertInstanceOf(
			'Doctrine\ORM\EntityManager',
			$this->helper->getEntityManager(),
			'->getContainer() instance Doctrine\ORM\EntityManager'
		);
		$this->assertSame($this->dc->entityManager, $this->helper->getEntityManager());
	}

	public function testGetName()
	{
		$this->assertEquals('entityManager', $this->helper->getName());
	}
}