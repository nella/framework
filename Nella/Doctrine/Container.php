<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine;

use Nette\DI;

/**
 * Doctrine service container
 *
 * @author	Patrik Votoček
 *
 * @property-read \Doctrine\ORM\EntityManager $entityManager
 * @property-read \Nette\DI\Container $context
 * @property-write string $defaultServiceClass
 */
class Container extends \Nella\Models\Container
{
	/** @var \Nette\DI\Container */
	private $context;
	/** @var string */
	protected $defaultServiceClass = 'Nella\Doctrine\Service';

	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(DI\Container $context)
	{
		$this->context = $context;
	}

	/**
	 * @return \Nette\DI\Container
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		$this->freeze();
		return $this->context->entityManager;
	}

	/**
	 * @return \Doctrine\DBAL\Migrations\Configuration\Configuration
	 */
	public function getMigrationConfiguration()
	{
		$this->freeze();
		return $this->context->migrationConfiguration;
	}

	/**
	 * @param string
	 * @return Service
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\InvalidStateException
	 */
	public function getService($entityClass)
	{
		if (isset($this->services[$entityClass])) {
			return $this->services[$entityClass];
		}

		if (!class_exists($entityClass)) {
			throw new \Nette\InvalidArgumentException("Class '$entityClass' does not exist'");
		} elseif (!\Nette\Reflection\ClassType::from($entityClass)->implementsInterface('Nella\Models\IEntity')) {
			throw new \Nette\InvalidArgumentException(
				"Entity '$entityClass' does not valid entity (must implements Nella\\Models\\IEntity)"
			);
		}

		$class = $this->defaultServiceClass;

		return $this->services[$entityClass] = new $class($this, $entityClass);
	}

	/**
	 * @param \Nette\DI\Container
	 * @param string
	 * @return Container
	 */
	public static function create(DI\Container $context, $sectionName = "database")
	{
		if (!isset($context->params[$sectionName])) {
			throw new \Nette\InvalidStateException("Doctrine configuration section '$section' does not exist");
		}

		$database = $context->params[$sectionName];
		if ($database instanceof \Nette\ArrayHash) {
			$database = $database->getIterator()->getArrayCopy();
		}

		$context->params['doctrine-config'] = \Nette\ArrayHash::from(array_merge(array(
			'productionMode' => $context->params['productionMode'],
			'proxyDir' => $context->expand("%appDir%/proxies"),
			'proxyNamespace' => 'App\Models\Proxies',
			'entityDirs' => array($context->params['appDir'], NELLA_FRAMEWORK_DIR),
			'migrations' => array(
				'name' => \Nella\Framework::NAME . " DB Migrations",
				'table' => "db_version",
				'directory' => $context->expand("%appDir%/migrations"),
				'namespace' => 'App\Models\Migrations',
			),
		), $database));

		if (!$context->hasService('userableListener')) {
			$context->addService('userableListener', function(DI\Container $context) {
				return new Listeners\Userable($context->user, $context->cacheStorage);
			}, array('listener'));
		}
		if (!$context->hasService('versionListener')) {
			$context->addService('versionListener', 'Nella\Doctrine\Listeners\Version', array('listener'));
		}
		if (!$context->hasService('validatorListener')) {
			$context->addService('validatorListener', function(DI\Container $context) {
				return new Listeners\Validator($context->validator);
			}, array('listener'));
		}
		if (!$context->hasService('mediaListener')) {
			$context->addService('mediaListener', function(DI\Container $context) {
				return new \Nella\Media\Listener($context->cacheStorage);
			}, array('listener'));
		}

		foreach (get_class_methods(get_called_class()) as $method) {
			if (\Nette\Utils\Strings::startsWith($method, 'createService')) {
				$name = strtolower(substr($method, 13, 1)) . substr($method, 14);
				if (!$context->hasService($name)) {
					$context->addService($name, callback(get_called_class(), $method));
				}
			}
		}
		return new static($context);
	}

	/**
	 * @return \Doctrine\DBAL\Logging\SQLLogger
	 */
	public static function createServiceLogger()
	{
		return Panel::register();
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Doctrine\ORM\Configuration
	 */
	public static function createServiceConfiguration(DI\Container $context)
	{
		$config = new \Doctrine\ORM\Configuration;

		// Cache
		$storage = $context->hasService('metadataCache') ? $context->metadataCache : new Cache($context->cacheStorage);
		$config->setMetadataCacheImpl($storage);
		$storage = $context->hasService('queryCache') ? $context->queryCache : new Cache($context->cacheStorage);
		$config->setQueryCacheImpl($storage);

		// Metadata
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver());

		// Proxies
		$config->setProxyDir($context->params['doctrine-config']['proxyDir']);
		$config->setProxyNamespace($context->params['doctrine-config']['proxyNamespace']);
		if ($context->params['doctrine-config']['productionMode']) {
			$config->setAutoGenerateProxyClasses(FALSE);
		} else {
			if ($context->hasService('logger')) {
				$config->setSQLLogger($context->logger);
			}
			$config->setAutoGenerateProxyClasses(TRUE);
		}

		return $config;
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Doctrine\DBAL\Event\Listeners\MysqlSessionInit
	 */
	public static function createServiceMysqlSessionInitListener(DI\Container $context)
	{
		return new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit($context->params['doctrine-config']['charset']);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Doctrine\Common\EventManager
	 */
	public static function createServiceEventManager(DI\Container $context)
	{
		$evm = new \Doctrine\Common\EventManager;
		foreach (array_keys($context->getServiceNamesByTag('listener')) as $name) {
			$evm->addEventSubscriber($context->getService($name));
		}

		return $evm;
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Doctrine\ORM\EntityManager
	 */
	public static function createServiceEntityManager(DI\Container $context)
	{
		$evm = $context->eventManager;
		if (key_exists('driver', $context->params['doctrine-config'])
			 && $context->params['doctrine-config']['driver'] == "pdo_mysql"
			 && key_exists('charset', $context->params['doctrine-config'])) {
			$evm->addEventSubscriber($context->mysqlSessionInitListener);
		}

		$context->freeze();
		$config = $context->params['doctrine-config']->getIterator()->getArrayCopy();
		return \Doctrine\ORM\EntityManager::create($config, $context->configuration, $evm);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Doctrine\DBAL\Migrations\Configuration\Configuration
	 */
	public static function createServiceMigrationConfiguration(DI\Container $context)
	{
		$config = new \Doctrine\DBAL\Migrations\Configuration\Configuration($context->entityManager->getConnection());
		$config->setName($context->params['doctrine-config']['migration']['name']);
		$config->setMigrationsTableName($context->params['doctrine-config']['migration']['table']);
		$config->setMigrationsDirectory($context->params['doctrine-config']['migration']['directory']);
		$config->setMigrationsNamespace($context->params['doctrine-config']['migration']['namespace']);
		$context->freeze();
		return $config;
	}
}
