<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests\NetteAddons\Doctrine\Config;

class MigrationsExtensionTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\NetteAddons\Doctrine\Config\MigrationsExtension */
	private $extension;

	public function setup()
	{
		$this->extension = new \Nella\NetteAddons\Doctrine\Config\MigrationsExtension;
		$this->extension->setCompiler(new \Nette\Config\Compiler(), 'doctrineMigrations');
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
		$compiler->expects($this->any())->method('getConfig')->will($this->returnValue(
			array('doctrineMigrations' => array('connection' => 'default'))
		));
		$compiler->expects($this->any())->method('getContainerBuilder')->will($this->returnValue($builder));

		$this->extension->setCompiler($compiler, 'doctrineMigrations');
		$this->extension->loadConfiguration();

		$this->assertTrue($builder->hasDefinition('doctrineMigrations.configuration'), "has 'configuration' definition");
		$this->assertTrue($builder->hasDefinition('doctrineMigrations.consoleOutput'), "has 'consoleOutput' definition");

		// commands
		$this->assertTrue($builder->hasDefinition('doctrineMigrations.consoleCommandDiff'), "has 'consoleCommandDiff' definition");
		$this->assertTrue($builder->hasDefinition('doctrineMigrations.consoleCommandExecute'), "has 'consoleCommandExecute' definition");
		$this->assertTrue($builder->hasDefinition('doctrineMigrations.consoleCommandGenerate'), "has 'consoleCommandGenerate' definition");
		$this->assertTrue($builder->hasDefinition('doctrineMigrations.consoleCommandMigrate'), "has 'consoleCommandMigrate' definition");
		$this->assertTrue($builder->hasDefinition('doctrineMigrations.consoleCommandStatus'), "has 'consoleCommandStatus' definition");
		$this->assertTrue($builder->hasDefinition('doctrineMigrations.consoleCommandVersion'), "has 'consoleCommandVersion' definition");
	}
}