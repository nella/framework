<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Localization\Config;

use Nette\Config\Compiler,
	Nette\Config\Configurator;

/**
 * Console compiler extension
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	const DEFAULT_EXTENSION_NAME = 'localization',
		COMMAND_TAG_NAME = 'consoleCommand',
		HELPER_TAG_NAME = 'consoleHelper';

	/** @var array */
	public $defaults = array(
		'storage' => array(
			'class' => 'Nella\Localization\Storages\GettextBinary',
			'arguments' => array('%appDir%'),
		),
		'modules' => array(NULL),
	);

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		if ($config['dummy'] == TRUE) {
			$builder->addDefinition($this->prefix('translator'))
				->setClass('Nella\Localization\DummyTranslator');
			return;
		}

		$storage = $builder->addDefinition($this->prefix('storage'));
		Compiler::parseService($storage, $config['storage']);

		$translator = $builder->addDefinition($this->prefix('translator'))
			->setClass('Nella\Localization\Translator', array($storage));

		foreach ($config['modules'] as $module) {
			$translator->addSetup('addModule', array($module));
		}
	}

	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = self::DEFAULT_EXTENSION_NAME)
	{
		$class = get_called_class();
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) use ($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}

