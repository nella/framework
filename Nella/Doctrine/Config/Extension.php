<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Doctrine\Config;

use Nella\Console\Config\Extension as CExtension,
	Nette\Diagnostics\Debugger,
	Nette\Config\Compiler,
	Nette\Config\Configurator,
	Nette\DI\Container,
	Doctrine\Common\Cache\Cache,
	Doctrine\Common\EventManager,
	Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

/**
 * Doctrine Nella Framework services.
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	const DEFAULT_EXTENSION_NAME = 'doctrine',
		EVENT_TAG_NAME = 'doctrineEvent';

	public $defaults = array(
		'debugger' => NULL,
		'connection' => array(
			'collation' => FALSE,
			'autowired' => FALSE,
		),
		'eventManager' => NULL,
		'metadataDriver' => NULL,
		'useSimleAnnotation' => FALSE,
		'autowired' => TRUE,
		'entityDirs' => array('%appDir%'),
		'proxy' => array(
			'dir' => '%appDir%/proxies',
			'namespace' => 'App\Model\Proxies',
			'autogenerate' => NULL,
		),
		'repositoryClass' => 'Nella\Doctrine\Repository',
		'annotationCacheDriver' => TRUE,
		'metadataCacheDriver' => TRUE,
		'queryCacheDriver' => TRUE,
		'resultCacheDriver' => NULL,
		'console' => FALSE,
	);

	/**
	 * @throws \Nette\InvalidStateException
	 */
	protected function verifyDoctrineVersion()
	{
		if (!class_exists('Doctrine\ORM\Version')) {
			throw new \Nette\InvalidStateException('Doctrine ORM does not exists');
		} elseif (\Doctrine\ORM\Version::compare('2.3.0') > 0) {
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

		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		// process custom doctrine services
		\Nette\Config\Compiler::parseServices($builder, $config, $this->name);

		$cache = $builder->addDefinition($this->prefix('cache'))
			->setClass('Nella\Doctrine\Cache', array('@cacheStorage'));

		if ($config['debugger'] === NULL) {
			$config['debugger'] = $builder->parameters['debugMode'];
		}
		if ($config['proxy']['autogenerate'] === NULL) {
			$config['proxy']['autogenerate'] = $builder->parameters['debugMode'];
		}

		$evm = $builder->addDefinition($this->prefix('eventManager'))
			->setClass('Doctrine\Common\EventManager')
			->addSetup(get_called_class().'::setupEventManager', array('@self', '@container'));
		if (isset($config['eventManager']) && $config['eventManager']) {
			$evm->setFactory($config['eventManager']);
		}

		$connection = $builder->addDefinition($this->prefix('connection'))
			->setClass('Doctrine\DBAL\Connection')
			->setFactory(get_called_class().'::createConnection', array($config, $evm));

		$metadataDriver = $builder->addDefinition($this->prefix('metadataDriver'));

		if (empty($config['metadataDriver'])) {
			if ($config['annotationCacheDriver'] === TRUE) {
				$config['annotationCacheDriver'] = $cache;
			}

			$reader = $builder->addDefinition($this->prefix('annotationReader'))
				->setClass('Doctrine\Common\Annotations\Reader')
				->setFactory(get_called_class().'::createConnection', array(
					$config['annotationCacheDriver'], $config['useSimleAnnotation']
				));

			$metadataDriver->setClass('Doctrine\ORM\Mapping\Driver\AnnotationDriver', array($reader, $config['entityDirs']));
		} else {
			$metadataDriver->setClass('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver')
				->setFactory($config['metadataDriver']);
		}

		$configuration = $builder->addDefinition($this->prefix('configuration'))
			->setClass('Doctrine\ORM\Configuration')
			->addSetup('setMetadataDriverImpl', array($metadataDriver))
			->addSetup('setDefaultRepositoryClassName', array($config['repositoryClass']))
			->addSetup('setProxyDir', array($config['proxy']['dir']))
			->addSetup('setProxyNamespace', array($config['proxy']['namespace']))
			->addSetup('', array($config['proxy']['autogenerate']));

		if ($config['metadataCacheDriver']) {
			$configuration->addSetup('setMetadataCacheImpl', array(
				$config['metadataCacheDriver'] === TRUE ? $cache : $config['metadataCacheDriver'],
			));
		}
		if ($config['queryCacheDriver']) {
			$configuration->addSetup('setQueryCacheImpl', array(
				$config['queryCacheDriver'] === TRUE ? $cache : $config['queryCacheDriver'],
			));
		}
		if ($config['resultCacheDriver']) {
			$configuration->addSetup('setResultCacheImpl', array(
				$config['resultCacheDriver'] === TRUE ? $cache : $config['resultCacheDriver'],
			));
		}

		$entityManager = $builder->addDefinition($this->prefix('entityManager'))
			->setClass('Doctrine\ORM\EntityManager')
			->setFactory('Doctrine\ORM\EntityManager::create', array($connection, $configuration, $evm));

		if ($config['console']) {
			$this->processConsole($entityManager, $connection);
		}
	}

	/**
	 * @param string|\Nette\DI\ServiceDefinition
	 * @param string|\Nette\DI\ServiceDefinition
	 */
	protected function processConsole($entityManager = NULL, $connection = NULL)
	{
		if (!class_exists('Nella\Console\Config\Extension')) {
			throw new \Nette\InvalidStateException('Missing console extension');
		}

		$builder = $this->getContainerBuilder();

		// DBAL
		$builder->addDefinition($this->prefix('consoleCommandDBALRunSql'))
			->setClass('Doctrine\DBAL\Tools\Console\Command\RunSqlCommand')
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandDBALImport'))
			->setClass('Doctrine\DBAL\Tools\Console\Command\ImportCommand')
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);

		// console commands - ORM
		$builder->addDefinition($this->prefix('consoleCommandORMCreate'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand')
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandORMUpdate'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand')
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandORMDrop'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand')
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandORMGenerateProxies'))
			->setClass('Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand')
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandORMRunDql'))
			->setClass('Doctrine\ORM\Tools\Console\Command\RunDqlCommand')
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);

		if ($entityManager) {
			$builder->addDefinition($this->prefix('consoleHelperEntityManager'))
				->setClass('Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper', array($entityManager))
				->addTag(CExtension::HELPER_TAG_NAME, 'em')
				->setAutowired(FALSE);
		}
		if ($connection) {
			$builder->addDefinition($this->prefix('consoleHelperConnection'))
				->setClass('Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper', array($connection))
				->addTag(CExtension::HELPER_TAG_NAME, 'db')
				->setAutowired(FALSE);
		}
	}

	/**
	 * @param \Doctrine\Common\EventManager
	 * @param \Nette\DI\Container
	 */
	public static function setupEventManager(EventManager $evm, Container $container)
	{
		foreach ($container->findByTag(static::EVENT_TAG_NAME) as $name => $value) {
			$evm->addEventSubscriber($container->getService($name));
		}
	}

	/**
	 * @param array
	 * @param \Doctrine\Common\EventManager|NULL
	 * @return \Doctrine\DBAL\Connection
	 */
	public static function createConnection(array $params, \Doctrine\Common\EventManager $evm)
	{
		$panel = NULL;
		$config = new \Doctrine\DBAL\Configuration;

		if (isset($params['debugger']) && $params['debugger'] === TRUE) {
			$panel = new \Nella\Doctrine\Diagnostics\ConnectionPanel;
			if (Debugger::$bar) {
				Debugger::$bar->addPanel($panel);
			}
			Debugger::$blueScreen->addPanel(array($panel, 'renderException'));
			$config->setSQLLogger($panel);
		} else {
			Debugger::$blueScreen->addPanel('Nette\Database\Diagnostics\ConnectionPanel::renderException');
		}

		$cfg = $params['connection'];
		$connection = \Doctrine\DBAL\DriverManager::getConnection($cfg, $config, $evm);

		if ($connection->getDatabasePlatform()->getName() == 'mysql' && isset($cfg['charset'])) {
			$evm->addEventSubscriber(
				new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit($cfg['charset'], $cfg['collation'])
			);
		}

		if ($panel && $panel->doExplains) {
			$panel->setConnection($connection);
		}

		return $connection;
	}

	/**
	 * @param \Doctrine\Common\Cache\Cache|NULL
	 * @param bool
	 * @return \Doctrine\Common\Annotations\Reader
	 */
	public static function createAnnotationReader(Cache $cache = NULL, $useSimple = FALSE)
	{
		// force load doctrine annotations
		\Doctrine\Common\Annotations\AnnotationRegistry::registerFile(
			dirname(\Nette\Reflection\ClassType::from('Doctrine\ORM\Version')->getFileName()).
				'/Mapping/Driver/DoctrineAnnotations.php'
		);

		if ($useSimple) {
			$reader = new \Doctrine\Common\Annotations\SimpleAnnotationReader;
			$reader->addNamespace('Doctrine\ORM\Mapping');
		} else {
			$reader = new \Doctrine\Common\Annotations\AnnotationReader;
		}

		if (!$cache) {
			return $reader;
		}

		return new \Doctrine\Common\Annotations\CachedReader($reader, $cache);
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

