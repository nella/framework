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
	 * @param array
	 * @return Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver
	 */
	public static function annotationDriver(array $paths = NULL)
	{
		$reader = new \Doctrine\Common\Annotations\AnnotationReader;
		$reader->setDefaultAnnotationNamespace('Doctrine\ODM\MongoDB\Mapping\\');
		$paths = $paths ?: array(APP_DIR . "/..");
		return new \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver($reader, $paths);
	}

	/**
	 * @param array
	 * @return Doctrine\MongoDB\Connection
	 */
	public static function connection(array $conf)
	{
		$dns = "mongodb://{$conf['username']}:{$conf['password']}@{$conf['host']}/{$conf['dbname']}";
		return new \Doctrine\MongoDB\Connection(new \Mongo($dns));
	}

	/**
	 * @param \Doctrine\ODM\MongoDB\Mapping\Driver\Driver
	 * @param \Doctrine\Common\Cache\Cache
	 * @param array
	 * @return Doctrine\ODM\MongoDB\Configuration
	 */
	public static function configuration(\Doctrine\ODM\MongoDB\Mapping\Driver\Driver $driver, \Doctrine\Common\Cache\Cache $cache, array $options = NULL)
	{
		$config = new \Doctrine\ODM\MongoDB\Configuration;

		// Proxies
		$config->setProxyDir(isset($options['proxyDir']) ? $options['proxyDir'] : APP_DIR . "/proxies");
		$config->setProxyNamespace(isset($options['proxyNamespace']) ? $options['proxyNamespace'] : 'App\Proxies');
		if (\Nette\Environment::isProduction()) {
			$config->setAutoGenerateProxyClasses(FALSE);
		} else {
			$config->setAutoGenerateProxyClasses(TRUE);
		}

		// Hydrators
		$config->setHydratorDir(isset($options['hydratorDir']) ? $options['hydratorDir'] : APP_DIR . "/hydrators");
		$config->setHydratorNamespace(isset($options['hydratorNamespace']) ? $options['hydratorNamespace'] : 'App\Hydrators');

		// Anotation driver
		$config->setMetadataDriverImpl($driver);

		// Cache
		$config->setMetadataCacheImpl($cache);

		// Default DB
		if (isset($options['database']['dbname'])) {
			$config->setDefaultDB($options['database']['dbname']);
		}

		return $config;
	}

	/**
	 * @param array
	 * @return Doctrine\ODM\MongoDB\DocumentManager
	 */
	public static function documentManager(\Doctrine\MongoDB\Connection $connection, \Doctrine\ODM\MongoDB\Configuration $config)
	{
		return \Doctrine\ODM\MongoDB\DocumentManager::create($connection, $config);
	}
}
