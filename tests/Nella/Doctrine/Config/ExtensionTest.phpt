<?php
/**
 * Test: Nella\Doctrine\Config\Extension
 *
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 *
 * @testcase Nella\Tests\Doctrine\Config\ExtensionTest
 */

namespace Nella\Tests\Doctrine\Config;

use Assert,
	Nella\Mocks\Config\Configurator,
	Nella\Doctrine\Config\Extension;

require_once __DIR__ . '/../../../bootstrap.php';
require_once MOCKS_DIR . '/Config/Configurator.php';

class ExtensionTest extends \TestCase
{
	public function testRegister()
	{
		$configurator = new Configurator;
		Extension::register($configurator);
		$compiler = $configurator->createCompilerMock();
		$configurator->onCompile($configurator, $compiler);

		$extension = $compiler->extensions[Extension::DEFAULT_EXTENSION_NAME];
		Assert::true($extension instanceof Extension);
	}

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

		Assert::true($connection instanceof \Doctrine\DBAL\Connection, 'instance');
		Assert::equal('sqlite', $connection->getDatabasePlatform()->getName(), 'platform');
		Assert::true(
			$connection->getConfiguration()->getSQLLogger() instanceof \Nella\Doctrine\Diagnostics\ConnectionPanel, 'panel'
		);
		Assert::same($evm, $connection->getEventManager(), 'same event manager');
	}

	public function testCreateAnnotationReaderSimple()
	{
		$reader = Extension::createAnnotationReader(NULL, TRUE);

		Assert::true($reader instanceof \Doctrine\Common\Annotations\Reader, 'interface');
		Assert::true($reader instanceof \Doctrine\Common\Annotations\SimpleAnnotationReader, 'instance');
	}

	public function testCreateAnnotationReaderNormal()
	{
		$reader = Extension::createAnnotationReader(NULL, FALSE);

		Assert::true($reader instanceof \Doctrine\Common\Annotations\Reader, 'interface');
		Assert::true($reader instanceof \Doctrine\Common\Annotations\AnnotationReader, 'instance');
	}

	public function testCreateAnnotationReaderCache()
	{
		$cache = new \Nella\Doctrine\Cache(new \Nette\Caching\Storages\DevNullStorage);
		$reader = Extension::createAnnotationReader($cache, FALSE);

		Assert::true($reader instanceof \Doctrine\Common\Annotations\Reader, 'interface');
		Assert::true($reader instanceof \Doctrine\Common\Annotations\CachedReader, 'instance');
	}
}
