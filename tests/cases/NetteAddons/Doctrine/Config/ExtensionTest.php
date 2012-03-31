<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\NetteAddons\Doctrine\Config;

class ExtensionTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\NetteAddons\Doctrine\Config\Extension */
	private $extension;

	public function setup()
	{
		$this->extension = new \Nella\NetteAddons\Doctrine\Config\Extension;
		$this->extension->setCompiler(new \Nette\Config\Compiler(), 'doctrine');
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nette\Config\CompilerExtension', $this->extension);
	}

	public function testLoadConfig()
	{
		$builder = new \Nette\DI\ContainerBuilder;
		$builder->parameters['appDir'] = __DIR__;
		$builder->parameters['database'] = array('diver' => 'pdo_sqlite', 'memory' => TRUE);
		$compiler = $this->getMock('Nette\Config\Compiler');
		$compiler->expects($this->any())->method('getContainerBuilder')->will($this->returnValue($builder));

		$this->extension->setCompiler($compiler, 'doctrine');
		$this->extension->loadConfiguration();

		// Commands
		$this->assertTrue($builder->hasDefinition('doctrine.consoleCommandDBALRunSql'), "has 'consoleCommandDBALRunSql' definition");
		$this->assertTrue($builder->hasDefinition('doctrine.consoleCommandDBALImport'), "has 'consoleCommandDBALImport' definition");
		$this->assertTrue($builder->hasDefinition('doctrine.consoleCommandORMCreate'), "has 'consoleCommandORMCreate' definition");
		$this->assertTrue($builder->hasDefinition('doctrine.consoleCommandORMUpdate'), "has 'consoleCommandORMUpdate' definition");
		$this->assertTrue($builder->hasDefinition('doctrine.consoleCommandORMDrop'), "has 'consoleCommandORMDrop' definition");
		$this->assertTrue($builder->hasDefinition('doctrine.consoleCommandORMGenerateProxies'), "has 'consoleCommandORMGenerateProxies' definition");
		$this->assertTrue($builder->hasDefinition('doctrine.consoleCommandORMRunDql'), "has 'consoleCommandORMRunDql' definition");
	}

	protected function createConnection()
	{
		$config = array(
			'driver' => 'pdo_sqlite',
			'memory' => TRUE,
		);
		return \Doctrine\DBAL\DriverManager::getConnection($config);
	}

	public function testCreateAnnotationReader()
	{
		$cache = new \Doctrine\Common\Cache\ArrayCache;

		$reader = $this->extension->createAnnotationReader($cache, array(__DIR__));

		$this->assertInstanceOf('Doctrine\Common\Annotations\Reader', $reader,
			'is instance Doctrine\Common\Annotations\Reader');
	}

	public function testCreateConfiguration()
	{
		$cache = new \Doctrine\Common\Cache\ArrayCache;
		$connection = $this->createConnection();
		$proxy = array(
			'dir' => $this->getContext()->parameters['tempDir'],
			'namespace' => 'NellaTests\Temp',
			'autogenerate' => FALSE
		);

		$tmp = new \Doctrine\ORM\Configuration;
		$driver = $tmp->newDefaultAnnotationDriver();

		$configuration = $this->extension->createConfiguration($driver, $cache, $cache, NULL, $proxy);

		$this->assertInstanceOf('Doctrine\ORM\Configuration', $configuration, 'is instanceof Doctrine\ORM\Configuration');

		$this->assertSame($cache, $configuration->getMetadataCacheImpl(), 'metadata cache');
		$this->assertSame($cache, $configuration->getQueryCacheImpl(), 'query cache');
		$this->assertNull($configuration->getResultCacheImpl(), 'result cache');

		$this->assertSame($driver, $configuration->getMetadataDriverImpl(), 'metadata driver');

		$this->assertEquals($proxy['dir'], $configuration->getProxyDir(), 'proxy dir');
		$this->assertEquals($proxy['namespace'], $configuration->getProxyNamespace(), 'proxy dir');
	}

	public function testCreateEventManager()
	{
		$connection = $this->createConnection(array('driver' => 'pdo_sqlite', 'memory' => TRUE));
		$evm = $this->extension->createEventManager($connection, $this->getContext());

		$this->assertInstanceOf('Doctrine\Common\EventManager', $evm, 'is instance of Doctrine\Common\EventManager');
		$this->assertSame($connection->getEventManager(), $evm, 'same as connection eventManager');
	}

	public function testCreateConnection()
	{
		$config = array(
			'driver' => 'pdo_sqlite',
			'memory' => TRUE,
		);

		$connection = $this->extension->createConnection($config);

		$this->assertInstanceOf('Doctrine\DBAL\Connection', $connection, 'is instance of Doctrine\DBAL\Connection');
		$this->assertEquals('pdo_sqlite', $connection->getDriver()->getName(), 'is sqlite driver');
	}

	public function testCreateConsole()
	{
		$context = new \Nette\DI\Container;
		$context->addService('entityManager', \Doctrine\Tests\Mocks\EntityManagerMock::create(
			new \Doctrine\DBAL\Connection(array(), new \Doctrine\DBAL\Driver\PDOSqlite\Driver)
		));
		$console = $this->extension->createConsole($context, new \Symfony\Component\Console\Helper\HelperSet);

		$this->assertInstanceOf('Symfony\Component\Console\Application', $console, 'is instance of Symfony\Component\Console\Application');
	}
}