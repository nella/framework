<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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
		'namespaces' => array(),
		'templateDirs' => array(
			'%appDir%' => 2,
		),
	);

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		if ($builder->hasDefinition('nette.presenterFactory')) {
			$def = $builder->getDefinition('nette.presenterFactory');
			$def->setClass('Nella\Application\PresenterFactory', array("@container"));

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
		foreach ($config['templateDirs'] as $dir => $priority) {
			if (\Nette\Utils\Validators::isNumericInt($dir)) {
				$def->addSetup('addDir', array($priority));
			} else {
				$def->addSetup('addDir', array($dir, $priority));
			}
		}
	}
}
