<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine;

/**
 * Doctrine service container
 *
 * @author	Patrik Votoček
 * 
 * @property-read \Doctrine\ORM\EntityManager $entityManager
 * @property-write string $defaultServiceClass
 */
class Container extends \Nette\DI\Container
{
	/** @var array */
	private $services = array();
	/** @var string */
	private $defaultServiceClass = 'Nella\Models\Service';
	
	/**
	 * @return Cache
	 */
	protected function createServiceCache()
	{
		return new Cache($this->getService('container')->getService('Nette\Caching\IStorage'));
	}
	
	/**
	 * @return \Doctrine\Common\Cache\Cache
	 */
	protected function createServiceMetadataCache()
	{
		
		return $this->getService('cache');
	}
	
	/**
	 * @return \Doctrine\Common\Cache\Cache
	 */
	protected function createServiceQueryCache()
	{
		
		return $this->getService('cache');
	}
	
	/**
	 * @return Panel
	 */
	protected function createServiceLogger()
	{
		return Panel::register();
	}
	
	/**
	 * @return \Doctrine\ORM\Configuration
	 */
	protected function createServiceConfiguration()
	{
		$config = new \Doctrine\ORM\Configuration;

		// Cache
		$config->setMetadataCacheImpl($this->getService('metadataCache'));
		$config->setQueryCacheImpl($this->getService('queryCache'));

		// Metadata
		$dirs = $this->getParam('entitiesDirs', array(APP_DIR, NELLA_FRAMEWORK_DIR));
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($dirs));

		// Proxies
		$config->setProxyDir(APP_DIR . "/proxies");
		$config->setProxyNamespace('App\Models\Proxies');
		if ($this->getService('container')->getParam('productionMode')) {
			$config->setAutoGenerateProxyClasses(FALSE);
		} else {
			if ($this->hasService('logger')) {
				$config->setSQLLogger($this->getService('logger'));
			}
			$config->setAutoGenerateProxyClasses(TRUE);
		}
		
		return $config;
	}
	
	/**
	 * @return \Doctrine\DBAL\Event\Listeners\MysqlSessionInit
	 */
	protected function createServiceMysqlSessionInitListener()
	{
		$configName = $this->getParam('config', 'database');
		$database = $this->getService('container')->getService('config')->$configName;
		return new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit($database['charset']);
	}
	
	/**
	 * @return \Nella\Models\Listeners\Timestampable 
	 */
	protected function createServiceTimestampableListener()
	{
		$cacheStorage = $this->getService('container')
			->getService('Nette\Caching\ICacheStorage');
		return new \Nella\Models\Listeners\Timestampable($cacheStorage);
	}
	
	/**
	 * @return \Nella\Models\Listeners\Userable
	 */
	protected function createServiceUserableListener()
	{
		$cacheStorage = $this->getService('container')
			->getService('Nette\Caching\ICacheStorage');
		$user = $this->getService('container')->getService('Nette\Web\IUser');
		$identity = $user->isLoggedIn() ? $user->identity->entity : NULL;
		return new \Nella\Models\Listeners\Userable($identity, $cacheStorage);
	}
	
	/**
	 * @return \Nella\Models\Listeners\Validator
	 */
	protected function createServiceValidatorListener()
	{
		$validator = $this->getService('container')->getService('validator');
		return new \Nella\Models\Listeners\Validator($validator);
	}
	
	/**
	 * @return \Nella\Models\Listeners\Version
	 */
	protected function createServiceVersionListener()
	{
		return new \Nella\Models\Listeners\Version;
	}
	
	/**
	 * @return \Doctrine\Common\EventManager
	 */
	protected function createServiceEventManager()
	{
		$evm = new \Doctrine\Common\EventManager;
		foreach ($this->getParam('listeners', array()) as $listener) {
			$evm->addEventSubscriber($this->getService($listener));
		}
		
		return $evm;
	}
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function createServiceEntityManager()
	{
		$configName = $this->getParam('config', 'database');
		$database = $this->getService('container')->getService('config')->$configName;
		
		$evm = $this->getService('eventManager');
		if (key_exists('driver', $database) && $database['driver'] == "pdo_mysql" && key_exists('charset', $database)) {
			$evm->addEventSubscriber($this->getService('mysqlSessionInitListener'));
		}
		
		$this->freeze();
		return \Doctrine\ORM\EntityManager::create((array) $database, $this->getService('configuration'), $evm);
	}
	
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->getService('entityManager');
	}
	
	/**
	 * @param string
	 * @return Container
	 * @throws \Nette\InvalidArgumentException
	 */
	public function setDefaultServiceClass($class)
	{
		if (class_exists($class)) {
			throw new \Nette\InvalidArgumentException("Entity service class '$class' does not exists");
		}
		
		$this->defaultServiceClass = $class;
		return $this;
	}
	
	/**
	 * @param \Nella\Models\Service
	 * @throws \Nette\InvalidArgumentException
	 */
	public function getEntityService($entity)
	{
		if (class_exists($entity)) {
			throw new \Nette\InvalidArgumentException("Entity class '$entity' does not exists");
		}
		
		if (!isset($this->services[$entity])) {
			$ref = \Nette\Reflection\ClassType::from($entity);
			if ($ref->hasAnnotation('service')) {
				$class = $ref->getAnnotation('service');
			} else {
				$class = $this->defaultServiceClass;
			}
			
			$this->services[$entity] = new $class($this->getEntityManager(), $entity);
		}
		
		return $this->services[$entity];
	}
	
	/**
	 * @param string
	 * @param \Nella\Models\Service
	 * @return Container
	 */
	public function setEntityService($entity, \Nella\Models\Service $service)
	{
		$this->updating();
		$this->services[$entity] = $service;
		return $this;
	}
}
