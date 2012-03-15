<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Config;

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
		return \Nella\SplClassLoader::getInstance();
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
	 * @return Compiler
	 */
	protected function createCompiler()
	{
		$compiler = new \Nette\Config\Compiler;

		$nette = new \Nette\Config\Extensions\NetteExtension;
		$nette->defaults['container']['debugger'] = TRUE;

		$compiler->addExtension('php', new \Nette\Config\Extensions\PhpExtension)
				->addExtension('constants', new \Nette\Config\Extensions\ConstantsExtension)
				->addExtension('nette', $nette)
				->addExtension('doctrine', new \Nella\Doctrine\Config\Extension)
				->addExtension('migrations', new \Nella\NetteAddons\Doctrine\Config\MigrationsExtension)
				->addExtension('nella', new Extensions\NellaExtension)
				->addExtension('media', new \Nella\Media\Config\Extension);

		return $compiler;
	}
}