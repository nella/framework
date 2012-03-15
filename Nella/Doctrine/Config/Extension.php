<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine\Config;

/**
 * Nella Framework doctrine extension
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nella\NetteAddons\Doctrine\Config\Extension
{
	/** @var array */
	public $emDefaults = array(
		'repositoryClass' => 'Nella\Doctrine\Repository',
	);

	/**
	 * Processes configuration data
	 *
	 * @throws \Nette\InvalidStateException
	 */
	public function loadConfiguration()
	{
		$this->entityManagerDefaults['entityDirs'][] = realpath(__DIR__ . "/../..");
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		/**
		 *  Add some stuff to Doctrine addon for Nella Framework
		 */

		// Ignore Testing dir for loading entityes
		if ($builder->hasDefinition($this->prefix('metadataDriver'))) {
			$builder->getDefinition($this->prefix('metadataDriver'))
				->addSetup('addIgnoredDir', array(__DIR__ . "/../../Testing"));
		}

		// Set default repostitory class
		if (isset($config['entityManagers'])) {
			foreach ($config['entityManagers'] as $name => $em) {
				$cfg = $em + $this->emDefaults;

				if ($builder->hasDefinition($this->configurationsPrefix($name.'AnnotationReader'))) {
					$builder->addDefinition($this->configurationsPrefix($name.'DiscriminatorMapDiscovery'))
						->setClass('Nella\Doctrine\Listeners\DiscriminatorMapDiscovery', array(
							$builder->getDefinition($this->configurationsPrefix($name.'AnnotationReader'))
						))
						->addTag('doctrineListener')
						->setAutowired(FALSE);
				}

				if ($builder->hasDefinition($this->configurationsPrefix($name))) {
					$builder->getDefinition($this->configurationsPrefix($name))
						->addSetup('setDefaultRepositoryClassName', array($cfg['repositoryClass']));
				}
			}
		}
	}

	/**
	 * @param \Doctrine\Common\Cache\Cache
	 * @param bool
	 * @return \Doctrine\Common\Annotations\CachedReader
	 */
	public static function createAnnotationReader(\Doctrine\Common\Cache\Cache $cache, $useSimple = FALSE)
	{
		require_once __DIR__ ."/../Mapping/DiscriminatorEntry.php";
		return parent::createAnnotationReader($cache, $useSimple);
	}
}