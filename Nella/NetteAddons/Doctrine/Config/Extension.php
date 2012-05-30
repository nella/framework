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
	Nette\DI\ContainerBuilder,
	Doctrine\Common\Cache\Cache,
	Nette\Framework,
	Nette\Diagnostics\Debugger,
	Nette\DI\Container,
	Nette\Utils\Strings;

/**
 * Doctrine Nella Framework services.
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	const CONNECTIONS_PREFIX = 'connections',
		ENTITY_MANAGERS_PREFIX = 'entityManagers',
		EVENT_MANAGERS_PREFIX = 'eventManagers',
		CONFIGURATIONS_PREFIX = 'configurations';

	/** @var array */
	public $connectionDefaults = array(
		'debugger' => TRUE,
		'collation' => FALSE,
		'eventManager' => NULL,
		'autowired' => FALSE,
	);
	/** @var array */
	public $entityManagerDefaults = array(
		'entityDirs' => array('%appDir%'),
		'proxyDir' => '%appDir%/proxies',
		'proxyNamespace' => 'App\Model\Proxies',
		'proxyAutogenerate' => NULL,
		'useAnnotationNamespace' => FALSE,
		'metadataFactory' => NULL,
		'metadataCacheDriver' => '@doctrine.cache',
		'queryCacheDriver' => '@doctrine.cache',
		'resultCacheDriver' => NULL,
		'console' => FALSE,
	);
	/** @var string|NULL */
	protected $consoleEntityManager;
	/** @var \Nette\Callback */
	protected $connectionFactory;
	/** @var \Nette\Callback */
	protected $annotationReaderFactory;
	/** @var \Nette\Callback */
	protected $metadataFactory;
	/** @var \Nette\Callback */
	protected $entityManagerFactory;

	/**
	 * @throws \Nette\InvalidStateException
	 */
	protected function verifyDoctrineVersion()
	{
		if (!class_exists('Doctrine\ORM\Version')) {
			throw new \Nette\InvalidStateException('Doctrine ORM does not exists');
		} elseif (\Doctrine\ORM\Version::compare('2.2.0') > 0) {
			throw new \Nette\InvalidStateException(
				'Doctrine version ' . \Doctrine\ORM\Version::VERSION . ' not supported (support only for 2.2+)'
			);
		}
	}

	/**
	 * Processes configuration data
	 *
	 * @throws \Nette\InvalidStateException
	 */
	public function loadConfiguration()
	{
		$this->verifyDoctrineVersion();

		$config = $this->getConfig(array(
			'connections' => array(),
			'entityManagers' => array(),
			'console' => array('entityManager' => NULL),
		));
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('cache'))
			->setClass('Nella\NetteAddons\Doctrine\Cache', array('@cacheStorage'));

		$this->consoleEntityManager = isset($config['console']['entityManager'])
			 ? $config['console']['entityManager'] : NULL;

		$this->processFactories();

		// process custom doctrine services
		\Nette\Config\Compiler::parseServices($builder, $config, $this->name);

		$this->processNestedAccessors();


		foreach ($config['connections'] as $name => $connection) {
			$cfg = $connection + $this->connectionDefaults;
			$this->processConnection($name, $cfg);
		}

		foreach ($config['entityManagers'] as $name => $em) {
			$cfg = $em + $this->entityManagerDefaults;

			if (isset($cfg['connection']) && is_array($cfg['connection'])) { // process connection sertting
				$this->processConnection($name, $cfg['connection'] + $this->connectionDefaults);
				$cfg['connection'] = $name;
			}

			$this->processEntityManager($name, $cfg);
		}

		$this->processConsole($this->consoleEntityManager);
	}

	protected function processFactories()
	{
		$builder = $this->getContainerBuilder();

		$this->connectionFactory = $builder->addDefinition($this->prefix('connection'))
			->setClass('Doctrine\DBAL\Connection')
			->setParameters(array('config', 'configuration', 'eventManager' => NULL))
			->setFactory(get_called_class().'::createConnection', array(
				'%config%', '%eventManager%'
			));

		$this->annotationReaderFactory = $builder->addDefinition($this->prefix('annotationReader'))
			->setClass('Doctrine\Common\Annotations\CachedReader')
			->setParameters(array('cache', 'useSimple' => FALSE))
			->setFactory(get_called_class().'::createAnnotationReader', array('%cache%', '%useSimple%'));

		$this->metadataFactory = $builder->addDefinition($this->prefix('metadataDriver'))
			->setClass('Nella\NetteAddons\Doctrine\Mapping\Driver\AnnotationDriver', array(
				'%annotationReader%','%entityDirs%'
			))
			->setParameters(array('annotationReader','entityDirs'));

		$this->entityManagerFactory = $builder->addDefinition($this->prefix('newEntityManager'))
			->setClass('Doctrine\ORM\EntityManager')
			->setParameters(array('connection', 'configuration', 'eventManager' => NULL))
			->setFactory('Doctrine\ORM\EntityManager::create', array(
				'%connection%', '%configuration%', '%eventManager%'
			));
	}

	protected function processNestedAccessors()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix(static::CONNECTIONS_PREFIX))
			->setClass('Nette\DI\NestedAccessor', array('@container', $this->prefix(static::CONNECTIONS_PREFIX)));

		$builder->addDefinition($this->prefix(static::ENTITY_MANAGERS_PREFIX))
			->setClass('Nette\DI\NestedAccessor', array('@container', $this->prefix(static::ENTITY_MANAGERS_PREFIX)));

		$builder->addDefinition($this->prefix(static::EVENT_MANAGERS_PREFIX))
			->setClass('Nette\DI\NestedAccessor', array('@container', $this->prefix(static::EVENT_MANAGERS_PREFIX)));

		$builder->addDefinition($this->prefix(static::CONFIGURATIONS_PREFIX))
			->setClass('Nette\DI\NestedAccessor', array('@container', $this->prefix(static::CONFIGURATIONS_PREFIX)));
	}

	/**
	 * @param string
	 * @param array
	 */
	protected function processConnection($name, array $config)
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->connectionsPrefix($name))
			->setClass($this->connectionFactory->class)
			->setFactory($this->connectionFactory, array($config, $config['eventManager']))
			->setAutowired($config['autowired']);
	}

	/**
	 * @param string
	 * @param array
	 */
	protected function processEntityManager($name, array $config)
	{
		$builder = $this->getContainerBuilder();

		if ($this->consoleEntityManager && $config['console']) {
			throw new \Nette\InvalidStateException('Console is allready set');
		} elseif ($config['console']) {
			$this->consoleEntityManager = $name;
		}

		// Configuration
		if (!$builder->hasDefinition($this->configurationsPrefix($name))) {
			if (!$builder->hasDefinition($this->configurationsPrefix($name.'AnnotationReader'))) {
				$builder->addDefinition($this->configurationsPrefix($name.'AnnotationReader'))
					->setClass($this->annotationReaderFactory->class)
					->setFactory($this->annotationReaderFactory, array(
						$this->prefix('@cache'), $config['useAnnotationNamespace']
					))->setAutowired(FALSE);
			}
			if (!$builder->hasDefinition($this->configurationsPrefix($name.'MetadataDriver'))) {
				$builder->addDefinition($this->configurationsPrefix($name.'MetadataDriver'))
					->setClass($this->metadataFactory->class)
					->setFactory($this->metadataFactory, array(
						$this->configurationsPrefix('@'.$name.'AnnotationReader'), $config['entityDirs']
					))->setAutowired(FALSE);
			}

			$proxy = array(
				'dir' => $config['proxyDir'],
				'namespace' => $config['proxyNamespace'],
				'autogenerate' => $config['proxyAutogenerate'] !== NULL ?
					$config['proxyAutogenerate'] : $builder->parameters['debugMode'],
			);

			$builder->addDefinition($this->configurationsPrefix($name))
				->setClass('Doctrine\ORM\Configuration')
				->setFactory(get_called_class().'::createConfiguration', array(
					$this->configurationsPrefix('@'.$name.'MetadataDriver'), $config['metadataCacheDriver'],
					$config['queryCacheDriver'], $config['resultCacheDriver'], $proxy, $config['metadataFactory']
				))->setAutowired(FALSE);
		}

		// Event manager
		if (!$builder->hasDefinition($this->eventManagersPrefix($name))) {
			$builder->addDefinition($this->eventManagersPrefix($name))
				->setClass('Doctrine\Common\EventManager')
				->setFactory(get_called_class().'::createEventManager', array(
					$this->connectionsPrefix('@'.$config['connection']), '@container'
				));
		}

		// Entity manager
		$builder->addDefinition($this->entityManagersPrefix($name))
			->setClass($this->entityManagerFactory->class)
			->setFactory($this->entityManagerFactory, array($this->connectionsPrefix('@'.$config['connection']),
				$this->configurationsPrefix('@'.$name), $this->eventManagersPrefix('@'.$name)
			));
	}

	/**
	 * @param string
	 */
	protected function processConsole($entityManager = NULL)
	{
		$builder = $this->getContainerBuilder();

		// DBAL
		$builder->addDefinition($this->prefix('consoleCommandDBALRunSql'))
			->setClass('Doctrine\DBAL\Tools\Console\Command\RunSqlCommand')
			->addTag('consoleCommnad')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandDBALImport'))
			->setClass('Doctrine\DBAL\Tools\Console\Command\ImportCommand')
			->addTag('consoleCommand')
			->setAutowired(FALSE);

		// console commands - ORM
		$builder->addDefinition($this->prefix('consoleCommandORMCreate'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand')
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandORMUpdate'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand')
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandORMDrop'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand')
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandORMGenerateProxies'))
			->setClass('Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand')
			->addTag('consoleCommand')
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandORMRunDql'))
			->setClass('Doctrine\ORM\Tools\Console\Command\RunDqlCommand')
			->addTag('consoleCommand')
			->setAutowired(FALSE);

		if ($entityManager && !$builder->hasDefinition($this->entityManagersPrefix($entityManager))) {
			throw new \Nette\InvalidStateException("Entity Manager '$entityManager' does not exist");
		} elseif ($entityManager) {
			$builder->addDefinition($this->prefix('consoleHelperset'))
				->setClass('Symfony\Component\Console\Helper\HelperSet')
				->setFactory(get_called_class().'::createConsoleHelperSet', array(
					$this->entityManagersPrefix('@'.$entityManager), '@container'
				));

			$builder->addDefinition($this->prefix('console'))
				->setClass('Symfony\Component\Console\Application')
				->setFactory(get_called_class().'::createConsole', array('@container'))
				->setAutowired(FALSE);
		}
	}

	/**
	 * @param string
	 * @return string
	 */
	protected function connectionsPrefix($id)
	{
		$name = Strings::startsWith($id, '@') ?
			('@'.static::CONNECTIONS_PREFIX.'.'.substr($id, 1)) : (static::CONNECTIONS_PREFIX.'.'.$id);
		return $this->prefix($name);
	}

	/**
	 * @param string
	 * @return string
	 */
	protected function entityManagersPrefix($id)
	{
		$name = Strings::startsWith($id, '@') ?
			('@'.static::ENTITY_MANAGERS_PREFIX.'.'.substr($id, 1)) : (static::ENTITY_MANAGERS_PREFIX.'.'.$id);
		return $this->prefix($name);
	}

	/**
	 * @param string
	 * @return string
	 */
	protected function eventManagersPrefix($id)
	{
		$name = Strings::startsWith($id, '@') ?
			('@'.static::EVENT_MANAGERS_PREFIX.'.'.substr($id, 1)) : (static::EVENT_MANAGERS_PREFIX.'.'.$id);
		return $this->prefix($name);
	}

	/**
	 * @param string
	 * @return string
	 */
	protected function configurationsPrefix($id)
	{
		$name = Strings::startsWith($id, '@') ?
			('@'.static::CONFIGURATIONS_PREFIX.'.'.substr($id, 1)) : (static::CONFIGURATIONS_PREFIX.'.'.$id);
		return $this->prefix($name);
	}

	/**
	 * @param array
	 * @param \Doctrine\Common\EventManager|NULL
	 * @return \Doctrine\DBAL\Connection
	 */
	public static function createConnection(array $config, \Doctrine\Common\EventManager $evm = NULL)
	{
		if (!$evm) {
			$evm = new \Doctrine\Common\EventManager;
		}

		if (isset($config['driver']) && $config['driver'] == 'pdo_mysql' && isset($config['charset'])) {
			$evm->addEventSubscriber(
				new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit($config['charset'], $config['collation'])
			);
		}

		$cfg = new \Doctrine\DBAL\Configuration;
		if (isset($config['debugger']) && $config['debugger'] === TRUE) {
			$panel = new \Nella\NetteAddons\Doctrine\Diagnostics\ConnectionPanel;
			if (Debugger::$bar) {
				Debugger::$bar->addPanel($panel);
			}
			Debugger::$blueScreen->addPanel(array($panel, 'renderException'));
			$cfg->setSQLLogger($panel);
		} elseif (isset($config['debugger'])) {
			Debugger::$blueScreen->addPanel('Nette\Database\Diagnostics\ConnectionPanel::renderException');
			$cfg->setSQLLogger($config['debugger']);
		}

		return \Doctrine\DBAL\DriverManager::getConnection($config, $cfg, $evm);
	}

	/**
	 * @param \Doctrine\Common\Cache\Cache
	 * @param bool
	 * @return \Doctrine\Common\Annotations\CachedReader
	 */
	public static function createAnnotationReader(Cache $cache, $useSimple = FALSE)
	{
		\Doctrine\Common\Annotations\AnnotationRegistry::registerFile(
			dirname(\Nette\Reflection\ClassType::from('Doctrine\ORM\Version')->getFileName()).
				"/Mapping/Driver/DoctrineAnnotations.php"
		);

		if ($useSimple) {
			$reader = new \Doctrine\Common\Annotations\SimpleAnnotationReader;
			$reader->addNamespace('Doctrine\ORM\Mapping');
		} else {
			$reader = new \Doctrine\Common\Annotations\AnnotationReader;
		}

		return new \Doctrine\Common\Annotations\CachedReader($reader, $cache);
	}

	/**
	 * @param \Doctrine\ORM\Mapping\Driver\Driver
	 * @param \Doctrine\Common\Cache\Cache
	 * @param \Doctrine\Common\Cache\Cache
	 * @param \Doctrine\Common\Cache\Cache
	 * @param array $proxy
	 * @param string
	 * @return \Doctrine\ORM\Configuration
	 */
	public static function createConfiguration(\Doctrine\ORM\Mapping\Driver\Driver $metadataDriver,
		Cache $metadataCache = NULL, Cache $queryCache = NULL, Cache $resultCache = NULL, array $proxy,
		$metadataFactoryClass = NULL)
	{
		$configuration = new \Doctrine\ORM\Configuration;

		// Cache
		if ($metadataCache) {
			$configuration->setMetadataCacheImpl($metadataCache);
		}
		if ($queryCache) {
			$configuration->setQueryCacheImpl($queryCache);
		}
		if ($resultCache) {
			$configuration->setResultCacheImpl($resultCache);
		}

		// Metadata
		$configuration->setMetadataDriverImpl($metadataDriver);
		if ($metadataFactoryClass) {
			$configuration->setClassMetadataFactoryName($metadataFactoryClass);
		}

		// Proxies
		$configuration->setProxyDir($proxy['dir']);
		$configuration->setProxyNamespace($proxy['namespace']);
		$configuration->setAutoGenerateProxyClasses($proxy['autogenerate']);

		return $configuration;
	}

	/**
	 * @param \Doctrine\DBAL\Connection
	 * @param \Nette\DI\Container
	 * @return \Doctrine\Common\EventManager
	 */
	public static function createEventManager(\Doctrine\DBAL\Connection $connection, \Nette\DI\Container $container)
	{
		$evm = $connection->getEventManager();
		foreach (array_keys($container->findByTag('doctrineListener')) as $name) {
			$evm->addEventSubscriber($container->getService($name));
		}

		return $evm;
	}

	/**
	 * @param \Doctrine\ORM\EntityManager
	 * @return \Symfony\Component\Console\Helper\HelperSet
	 */
	public static function createConsoleHelperSet(\Doctrine\ORM\EntityManager $em)
	{
		$helperSet = new \Symfony\Component\Console\Helper\HelperSet;
		$helperSet->set(new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em), 'em');
		$helperSet->set(new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()), 'db');
		$helperSet->set(new \Symfony\Component\Console\Helper\DialogHelper, 'dialog');

		return $helperSet;
	}

	/**
	 * @param \Nette\DI\Container
	 * @param \Symfony\Component\Console\Helper\HelperSet
	 * @return \Symfony\Component\Console\Application
	 */
	public static function createConsole(Container $container, \Symfony\Component\Console\Helper\HelperSet $helperSet)
	{
		$app = new \Symfony\Component\Console\Application(
			Framework::NAME . " Command Line Interface", Framework::VERSION
		);
		if (class_exists('Nella\Framework')) {
			$app = new \Symfony\Component\Console\Application(
				\Nella\Framework::NAME . " Command Line Interface", \Nella\Framework::VERSION
			);
		}

		$app->setHelperSet($helperSet);
		$app->setCatchExceptions(FALSE);

		$commands = array();
		foreach (array_keys($container->findByTag('consoleCommand')) as $name) {
			$commands[] = $container->getService($name);
		}
		$app->addCommands($commands);

		return $app;
	}

	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = 'doctrine')
	{
		$class = get_called_class();
		$configurator->onCompile[] = function(Configurator $configurator, \Nette\Config\Compiler $compiler) use($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}