<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Config\Extensions;

/**
 * Model extension
 *
 * Registering default dao services
 *
 * @author	Patrik Votoček
 */
class ModelExtension extends \Nette\Config\CompilerExtension
{
	const SERVICES_KEY = 'services';

	/** @var array */
	public $defaults = array(
		self::SERVICES_KEY => array(
			'media.file' => "@media.fileDao",
			'media.image' => "@media.imageDao",
			'media.imageFormat' => "@media.imageFormatDao",
		)
	);

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		if (!isset($config['entityManager'])) {
			throw new \Nette\InvalidStateException('Model extension entity manager not set');
		}

		$builder->addDefinition($this->prefix('entityManager'))
			->setClass('Doctrine\ORM\EntityManager')
			->setFactory($config['entityManager'])
			->setAutowired(FALSE);
		unset($config['entityManager']);

		foreach ($config[self::SERVICES_KEY] as $name => $def) {
			\Nette\Config\Compiler::parseService($builder->addDefinition($this->prefix($name)), $def, FALSE);
		}
		unset($config[self::SERVICES_KEY]);

		foreach ($config as $name => $data) {
			$this->setupItem($builder, $name, $data);
		}
	}

	/**
	 * @param \Nette\DI\ContainerBuilder
	 * @param string
	 * @param mixed
	 * @param string|NULL
	 */
	protected function setupItem(\Nette\DI\ContainerBuilder $builder, $name, $data, $parent = NULL)
	{
		$fullname = $parent ? ("$parent.$name") : $name;

		if (is_array($data) && !isset($data['entity'])) {
			$builder->addDefinition($this->prefix($fullname))
				->setClass('Nette\DI\NestedAccessor', array('@container', $this->prefix($fullname)));
			foreach ($data as $name => $item) {
				$this->setupItem($builder, $name, $item, $fullname);
			}
		} elseif (is_array($data) && isset($data['entity'])) {
			$params = array($this->prefix('@entityManager'), $data['entity'], NULL);
			if (isset($data['service'])) {
				$params[2] = $data['service'];
			}
			if (isset($data['class'])) {
				$params[3] = $data['class'];
			}

			$def = $builder->addDefinition($this->prefix($fullname));
			$def->setClass('Nella\Doctrine\Dao')
				->setFactory(get_called_class()."::factory", $params);
			if (isset($data['setup'])) {
				foreach ($data['setup'] as $target => $args) {
					$def->addSetup($target, $args);
				}
			}
		} elseif (is_string($data) && \Nette\Utils\Strings::startsWith($data, '@')) {
			$builder->addDefinition($this->prefix($fullname))->setClass('Nella\Doctrine\Dao')->setFactory($data);
		} elseif (is_string($data) && class_exists($data)) {
			$builder->addDefinition($this->prefix($fullname))
				->setClass('Nella\Doctrine\Dao')
				->setFactory(get_called_class()."::factory", array($this->prefix('@entityManager'), $data));
		} else { // really?
			$builder->addDefinition($this->prefix($fullname))
				->setClass('Nella\Doctrine\Dao')
				->setFactory($data);
		}
	}

	/**
	 * @param \Doctrine\ORM\EntityManager
	 * @param string
	 * @param object|NULL
	 * @param string
	 * @return object
	 */
	public static function factory(\Doctrine\ORM\EntityManager $em, $entityClassName, $service = NULL, $class = 'Nella\Doctrine\Dao')
	{
		$ref = \Nette\Reflection\ClassType::from($class);
		return $ref->newInstanceArgs(array(
			'em' => $em,
			'repository' => $em->getRepository($entityClassName),
			'service' => $service,
		));
	}
}
