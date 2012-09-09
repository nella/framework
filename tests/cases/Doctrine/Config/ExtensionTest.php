<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\Doctrine\Config;

use Nella\Doctrine\Config\Extension,
	Nella\Doctrine\Cache,
	Nette\DI\Container;

class ExtensionTest extends \Nella\Testing\TestCase
{
	public function testCreateConnection()
	{
		$config = array(
			'debugger' => TRUE,
			'connection' => array(
				'driver' => 'pdo_sqlite',
				'memory' => TRUE,
			)
		);
		$evm = new \Doctrine\Common\EventManager;

		$connection = Extension::createConnection($config, $evm);

		$this->assertInstanceOf('Doctrine\DBAL\Connection', $connection, 'instance');
		$this->assertEquals('sqlite', $connection->getDatabasePlatform()->getName(), 'platform');
		$this->assertInstanceOf(
			'Nella\Doctrine\Diagnostics\ConnectionPanel', $connection->getConfiguration()->getSQLLogger(), 'panel'
		);
		$this->assertSame($evm, $connection->getEventManager(), 'same event manager');
	}

	public function testCreateAnnotationReaderSimple()
	{
		$reader = Extension::createAnnotationReader(NULL, TRUE);

		$this->assertInstanceOf('Doctrine\Common\Annotations\Reader', $reader, 'interface');
		$this->assertInstanceOf('Doctrine\Common\Annotations\SimpleAnnotationReader', $reader, 'instance');
	}

	public function testCreateAnnotationReaderNormal()
	{
		$reader = Extension::createAnnotationReader(NULL, FALSE);

		$this->assertInstanceOf('Doctrine\Common\Annotations\Reader', $reader, 'interface');
		$this->assertInstanceOf('Doctrine\Common\Annotations\AnnotationReader', $reader, 'instance');
	}

	public function testCreateAnnotationReaderCache()
	{
		$cache = new Cache(new \Nette\Caching\Storages\DevNullStorage);
		$reader = Extension::createAnnotationReader($cache, FALSE);

		$this->assertInstanceOf('Doctrine\Common\Annotations\Reader', $reader, 'interface');
		$this->assertInstanceOf('Doctrine\Common\Annotations\CachedReader', $reader, 'instance');
	}
}

