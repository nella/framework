<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Doctrine\Config;

use Nette\Config\Configurator,
	Nette\DI\ContainerBuilder;

/**
 * Doctrine migration Nella Framework services.
 *
 * @author	Patrik Votoček
 *
 * @property array defaults
 */
class MigrationsExtension extends \Nette\Config\CompilerExtension
{
	/**
	 * @return array
	 */
	private function getDefaults()
	{
		$name = \Nette\Framework::NAME . " DB Migrations";
		if (class_exists('Nella\Framework')) {
			$name = \Nella\Framework::NAME . " DB Migrations";
		}

		return array(
			'name' => $name,
			'table' => "db_version",
			'directory' => "%appDir%/migrations",
			'namespace' => 'App\Model\Migrations',
		);
	}

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		if (!$this->getConfig()) { // ignore migrations if config section not exist
			return;
		}

		$config = $this->getConfig($this->getDefaults());
		$builder = $this->getContainerBuilder();

		if (!isset($config['connection'])) {
			throw new \Nette\InvalidStateException('Migration database connection does not set');
		}

		$this->processConsole();

		$builder->addDefinition($this->prefix('configuration'))
			->setClass('Doctrine\DBAL\Migrations\Configuration\Configuration', array(
				$config['connection'], $this->prefix('@consoleOutput')
			))
			->addSetup('setName', array($config['name']))
			->addSetup('setMigrationsTableName', array($config['table']))
			->addSetup('setMigrationsDirectory', array($config['directory']))
			->addSetup('setMigrationsNamespace', array($config['namespace']))
			->addSetup('registerMigrationsFromDirectory', array($config['directory']));
	}

	protected function processConsole()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('consoleOutput'))
			->setClass('Doctrine\DBAL\Migrations\OutputWriter')
			->setFactory(get_called_class().'::createConsoleOutput')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('consoleCommandDiff'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand')
			->addSetup('setMigrationConfiguration', array($this->prefix('@configuration')))
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandExecute'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand')
			->addSetup('setMigrationConfiguration', array($this->prefix('@configuration')))
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandGenerate'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand')
			->addSetup('setMigrationConfiguration', array($this->prefix('@configuration')))
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandMigrate'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand')
			->addSetup('setMigrationConfiguration', array($this->prefix('@configuration')))
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandStatus'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand')
			->addSetup('setMigrationConfiguration', array($this->prefix('@configuration')))
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandVersion'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand')
			->addSetup('setMigrationConfiguration', array($this->prefix('@configuration')))
			->addTag('consoleCommand')
			->setAutowired(FALSE);
	}

	/**
	 * @return \Symfony\Component\Console\Output\ConsoleOutput
	 */
	public static function createConsoleOutput()
	{
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		return new \Doctrine\DBAL\Migrations\OutputWriter(function($message) use($output) {
			$output->write($message, TRUE);
		});
	}

	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = 'migrations')
	{
		$class = get_called_class();
		$configurator->onCompile[] = function(Configurator $configurator, \Nette\Config\Compiler $compiler) use($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}