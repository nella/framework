<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Doctrine;

require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . "/../Mocks/EntityManagerMock.php";

class MigrationConfigurationHelperTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\DI\ContainerHelper */
	private $helper;

	public function setup()
	{
		parent::setup();
		$container = new \Nette\DI\Container;
		$container->addService('migrationConfiguration', new \Doctrine\DBAL\Migrations\Configuration\Configuration(
			new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver)
		));
		$container = new \Nella\Doctrine\Container($container);
		$this->helper = new \Nella\Doctrine\MigrationConfigurationHelper($container);
	}

	public function testGetContainer()
	{
		$this->assertInstanceOf(
			'Doctrine\DBAL\Migrations\Configuration\Configuration',
			$this->helper->getConfiguration(),
			'->getConfiguration() instance Doctrine\DBAL\Migrations\Configuration\Configuration'
		);
	}

	public function testGetName()
	{
		$this->assertEquals('migration-configuration', $this->helper->getName());
	}
}