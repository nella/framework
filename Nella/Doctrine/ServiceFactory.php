<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Doctrine;

/**
 * Model service factories
 *
 * @author	Patrik Votoček
 */
class ServiceFactory extends \Nette\Object
{
	/**
	 * @throws InvalidStateException
	 */
	final public function __construct()
	{
		throw new \InvalidStateException("Cannot instantiate static class " . get_called_class());
	}
	
	/**
	 * @param \Doctrine\Common\Cache\Cache
	 * @param array
	 * @param string
	 * @param string
	 * @return \Doctrine\ORM\Configuration
	 */
	public static function configuration(\Doctrine\Common\Cache\Cache $cache = NULL, array $dirs = NULL, \Doctrine\DBAL\Logging\SQLLogger $logger = NULL, $proxyDir = NULL, $proxyNamespace = 'App\Models\Proxies')
	{
		$config = new \Doctrine\ORM\Configuration;

		// Cache
		$cache = $cache ?: new \Doctrine\Common\Cache\ArrayCache;
		$config->setMetadataCacheImpl($cache);
		$config->setQueryCacheImpl($cache);

		// Metadata
		$dirs = $dirs ?: array(APP_DIR, NELLA_FRAMEWORK_DIR);
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($dirs));

		// Proxies
		$proxyDir = $proxyDir ?: APP_DIR . "/proxies";
		$config->setProxyDir($proxyDir);
		$config->setProxyNamespace($proxyNamespace);
		if (\Nette\Environment::isProduction()) {
			$config->setAutoGenerateProxyClasses(FALSE);
		} else {
			$config->setAutoGenerateProxyClasses(TRUE);
		}
		
		// Logger
		if (isset($logger)) {
			$config->setSQLLogger($logger);
		}

		return $config;
	}
	
	/**
	 * @param array
	 * @param \Doctrine\ORM\Configuration
	 * @param \Doctrine\Common\EventManager
	 * @param \Doctrine\DBAL\Event\Listeners\MysqlSessionInit
	 * @return \Doctrine\ORM\EntityManager
	 */
	public static function entityManager(array $database, \Doctrine\ORM\Configuration $configuration = NULL, \Doctrine\Common\EventManager $event = NULL, \Doctrine\DBAL\Event\Listeners\MysqlSessionInit $mysqlEvent = NULL)
	{
		// Special event for MySQL
		if ($event && $mysqlEvent) {
			$event->addEventSubscriber($mysqlEvent);
		}

		// Entity manager
		$configuration = $configuration ?: self::configuration();
		return \Doctrine\ORM\EntityManager::create($database, $configuration, $event);
	}
}
