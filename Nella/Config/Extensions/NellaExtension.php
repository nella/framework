<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Config\Extensions;

/**
 * Nella Framework extension
 *
 * Registering default dao services
 *
 * @author	Patrik Votoček
 */
class NellaExtension extends \Nette\Config\CompilerExtension
{
	/** @var array */
	public $defaults = array(
		'useModuleSuffix' => TRUE,
		'namespaces' => array(
			'App' => 9,
		),
		'template' => array(
			'dirs' => array(
				'%appDir%' => 2,
			),
			'debugger' => TRUE,
		),
	);

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		if ($builder->hasDefinition('nette.presenterFactory')) {
			$def = $builder->getDefinition('nette.presenterFactory');
			$def->setClass('Nella\Application\PresenterFactory', array('@container'));
			$def->addSetup('$useModuleSuffix', array($config['useModuleSuffix']));

			foreach ($config['namespaces'] as $namespace => $priority) {
				if (\Nette\Utils\Validators::isNumericInt($namespace)) {
					$def->addSetup('addNamespace', array($priority));
				} else {
					$def->addSetup('addNamespace', array($namespace, $priority));
				}
			}
		}

		$def = $builder->addDefinition($this->prefix('templateFilesFormatter'));
		$def->setClass('Nella\Templating\TemplateFilesFormatter');
		$def->addSetup('$useModuleSuffix', array($config['useModuleSuffix']));
		foreach ($config['template']['dirs'] as $dir => $priority) {
			if (\Nette\Utils\Validators::isNumericInt($dir)) {
				$def->addSetup('addDir', array($priority));
			} else {
				$def->addSetup('addDir', array($dir, $priority));
			}
		}
		if ($config['template']['debugger']) {
			$logger = $builder->addDefinition($this->prefix('templateFilesFormatterLogger'));
			$logger->setClass('Nella\Templating\Diagnostics\FilesPanel');
			$logger->addSetup('Nette\Diagnostics\Debugger::$bar->addPanel(?)', array('@self'));
			$def->addSetup('setLogger', array($logger));
		}

		if ($builder->hasDefinition('nette.latte')) {
			$builder->getDefinition('nette.latte')
				->addSetup('Nella\Latte\Macros\UIMacros::factory', array('@self'));
		}
	}
}

