<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Config;

use Nella\Framework,
	Nella\SplClassLoader,
	Nella\Console\Config\Extension as ConsoleExtension,
	Nella\Doctrine\Config\Extension as DoctrineExtension,
	Nella\Doctrine\Config\MigrationsExtension as MigrationsExtension,
	Nella\Media\Config\Extension as MediaExtension,
	Nella\Event\Config\Extension as EventExtension,
	Nella\Diagnostics\Config\Extension as DiagnosticsExtension,
	Nella\Security\Config\Extension as SecurityExtension,
	Nella\Model\Config\Extension as ModelExtension;

/**
 * Initial system DI container generator.
 *
 * @author	Patrik Votoček
 *
 * @property-read \Nella\SplClassLoader $splClassLoader
 */
class Configurator extends \Nette\Config\Configurator
{
	/**
	 * @return \Nella\SplClassLoader
	 */
	public function getSplClassLoader()
	{
		return SplClassLoader::getInstance();
	}

	/**
	 * @param string
	 * @param string|bool
	 * @return \Nette\Config\Configurator
	 */
	public function addConfig($file, $section = self::NONE)
	{
		return parent::addConfig($file, $section);
	}

	/**
	 * @param string
	 * @param string|bool
	 * @return \Nette\Config\Configurator
	 */
	public function addConfigIfExist($file, $section = self::NONE)
	{
		if (!file_exists($file)) {
			return $this;
		}

		return $this->addConfig($file, $section);
	}

	/**
	 * @return \Nette\Config\Compiler
	 */
	protected function createCompiler()
	{
		$compiler = parent::createCompiler();
		if (isset($compiler->extensions['nette'])) {
			$nette = $compiler->extensions['nette'];
			$nette->defaults['container']['debugger'] = $this->parameters['debugMode'];
		}

		$console = new ConsoleExtension;
		$console->defaults['name'] = Framework::NAME;
		$console->defaults['version'] = Framework::VERSION;
		$doctrine = new DoctrineExtension;
		$doctrine->defaults['entityDirs'][] = __DIR__ . '/../';
		$doctrine->defaults['console'] = TRUE;
		$doctrine->defaults['repositoryClass'] = 'Nella\Model\Repository';
		$migrations = new MigrationsExtension;
		$migrations->defaultName = Framework::NAME . ' DB Migrations';
		$media = new MediaExtension;
		$media->defaults['entityManager'] = '@' . DoctrineExtension::DEFAULT_EXTENSION_NAME . '.entityManager';

		$compiler->addExtension(DiagnosticsExtension::DEFAULT_EXTENSION_NAME, new DiagnosticsExtension)
			->addExtension(ConsoleExtension::DEFAULT_EXTENSION_NAME, $console)
			->addExtension(DoctrineExtension::DEFAULT_EXTENSION_NAME, $doctrine)
			->addExtension(MigrationsExtension::DEFAULT_EXTENSION_NAME, $migrations)
			->addExtension(MediaExtension::DEFAULT_EXTENSION_NAME, $media)
			->addExtension(SecurityExtension::DEFAULT_EXTENSION_NAME, new SecurityExtension)
			->addExtension(ModelExtension::DEFAULT_EXTENSION_NAME, new ModelExtension)
			->addExtension(EventExtension::DEFAULT_EXTENSION_NAME, new EventExtension)
			->addExtension(Extensions\NellaExtension::DEFAULT_EXTENSION_NAME, new Extensions\NellaExtension);

		return $compiler;
	}
}

