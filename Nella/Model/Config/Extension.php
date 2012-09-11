<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Model\Config;

use Doctrine\ORM\EntityManager,
	Nette\Config\Compiler,
	Nette\Config\Configurator,
	Nette\Utils\Strings;

/**
 * Model extension
 *
 * Registering default facade services
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	const DEFAULT_EXTENSION_NAME = 'model',
		SERVICES_KEY = 'services';

	/** @var array */
	public $defaults = array(
		self::SERVICES_KEY => array(
			'media.file' => '@media.fileFacade',
			'media.image' => '@media.imageFacade',
			'media.imageFormat' => '@media.imageFormatFacade',
		)
	);

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		if (!isset($config['entityManager'])) {
			throw new \Nette\InvalidStateException('Model extension entity manager not set');
		}

		foreach ($config[self::SERVICES_KEY] as $name => $def) {
			\Nette\Config\Compiler::parseService($builder->addDefinition($this->prefix($name)), $def, FALSE);
		}
		unset($config[self::SERVICES_KEY]);

		$entityManager = Strings::startsWith($config['entityManager'], '@')
			? $config['entityManager'] : ('@' . $config['entityManager']);
		foreach ($config as $name => $data) {
			$this->setupItem($entityManager, $name, $data);
		}
	}

	/**
	 * @param \Nette\DI\ContainerBuilder
	 * @param string
	 * @param mixed
	 * @param string|NULL
	 */
	protected function setupItem($entityManager, $name, $data, $parent = NULL)
	{
		$builder = $this->getContainerBuilder();

		$fullname = $parent ? ("$parent.$name") : $name;

		if (is_array($data) && !isset($data['entity'])) {
			$builder->addDefinition($this->prefix($fullname))
				->setClass('Nette\DI\NestedAccessor', array('@container', $this->prefix($fullname)));
			foreach ($data as $name => $item) {
				$this->setupItem($entityManager, $name, $item, $fullname);
			}
		} elseif (is_array($data) && isset($data['entity'])) {
			$params = array($entityManager, $data['entity'], NULL);
			if (isset($data['service'])) {
				$params[2] = $data['service'];
			}
			if (isset($data['class'])) {
				$params[3] = $data['class'];
			}

			$def = $builder->addDefinition($this->prefix($fullname));
			$def->setClass('Nella\Model\Facade')
				->setFactory(get_called_class().'::factory', $params);
			if (isset($data['setup'])) {
				foreach ($data['setup'] as $setup) {
					$def->addSetup($setup->value, $setup->attributes);
				}
			}
		} elseif (is_string($data) && \Nette\Utils\Strings::startsWith($data, '@')) {
			$builder->addDefinition($this->prefix($fullname))->setClass('Nella\Model\Facade')->setFactory($data);
		} elseif (is_string($data) && class_exists($data)) {
			$builder->addDefinition($this->prefix($fullname))
				->setClass('Nella\Model\Facade')
				->setFactory(get_called_class().'::factory', array($entityManager, $data));
		} else {
			$builder->addDefinition($this->prefix($fullname))
				->setClass('Nella\Model\Facade')
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
	public static function factory(EntityManager $em, $entity, $service = NULL, $class = 'Nella\Model\Facade')
	{
		$ref = \Nette\Reflection\ClassType::from($class);
		return $ref->newInstanceArgs(array(
			'em' => $em,
			'repository' => $em->getRepository($entity),
			'service' => $service,
		));
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

