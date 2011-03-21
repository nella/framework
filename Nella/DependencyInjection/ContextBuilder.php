<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\DependencyInjection;

use Nette\Environment, 
	Nette\Config\Config, 
	Nella\FreezableArray;

/**
 * Context builder
 * 
 * @author	Patrik Votoček
 * @author	David Grudl
 * 
 * @property-write string $contextClass
 * @property-read IContext $context
 */
class ContextBuilder extends \Nette\Configurator
{
	/** @var string */
	private $contextClass = 'Nella\DependencyInjection\Context';
	/** @var array */
	private $autoRunServices = array();
	
	/** @var array */
	public $onBeforeLoad = array();
	/** @var array */
	public $onAfterLoad = array();
	
	/**
	 * @param string
	 * @return ContextBuilder
	 * @throws \InvalidArgumentException
	 */
	public function setContextClass($class)
	{
		if (!class_exists($class)) {
			throw new \InvalidArgumentException("Context class '$class' is not exist");
		}
		$ref = new \Nette\Reflection\ClassReflection($class);
		if (!$ref->implementsInterface('Nella\DependencyInjection\IContext')) {
			throw new \InvalidArgumentException("Context class '$class' is not valid 'Nella\DependencyInjection\IContext'");
		}
		
		$this->contextClass = $class;
		return $this;
	}
	
	/**
	 * @return IContext
	 */
	public function getContext()
	{
		return Environment::getContext();
	}
	
	/**
	 * @param string
	 */
	protected function loadEnvironmentName($name)
	{
		Environment::setVariable('environment', $name);
		$this->getContext()->environment = $name;
	}
	
	/**
	 * @param Config
	 * @throws \NotSupportedException
	 */
	protected function loadIni(Config $config)
	{
		if (PATH_SEPARATOR !== ';' && isset($config->include_path)) {
			$config->include_path = str_replace(';', PATH_SEPARATOR, $config->include_path);
		}

		foreach (clone $config as $key => $value) { // flatten INI dots
			if ($value instanceof Config) {
				unset($config->$key);
				foreach ($value as $k => $v) {
					$config->{"$key.$k"} = $v;
				}
			}
		}

		foreach ($config as $key => $value) {
			if (!is_scalar($value)) {
				throw new \InvalidStateException("Configuration value for directive '$key' is not scalar.");
			}

			if ($key === 'date.timezone') { // PHP bug #47466
				date_default_timezone_set($value);
			}

			if (function_exists('ini_set')) {
				ini_set($key, $value);
			} else {
				switch ($key) {
					case 'include_path':
						set_include_path($value);
						break;
					case 'iconv.internal_encoding':
						iconv_set_encoding('internal_encoding', $value);
						break;
					case 'mbstring.internal_encoding':
						mb_internal_encoding($value);
						break;
					case 'date.timezone':
						date_default_timezone_set($value);
						break;
					case 'error_reporting':
						error_reporting($value);
						break;
					case 'ignore_user_abort':
						ignore_user_abort($value);
						break;
					case 'max_execution_time':
						set_time_limit($value);
						break;
					default:
						if (ini_get($key) != $value) { // intentionally ==
							throw new \NotSupportedException('Required function ini_set() is disabled.');
						}
				}
			}
		}
	}
	
	/**
	 * @param Config $config
	 */
	protected function loadParameters(Config $config)
	{
		foreach ($config as $key => $value) {
			if ($key == "variables" && $value instanceof Config) {
				foreach ($value as $k => $v) {
					$this->getContext()->setParameter($k, $v);
					Environment::setVariable($k, $v);
				}
			} elseif ($key != "php" && $key != "services") {
				$tmp = $value instanceof Config ? $value->toArray() : $value;
				$this->getContext()->setParameter($key, $tmp);
			}
		}
	}
	
	protected function loadDefaultServices()
	{
		$this->loadServices($this->defaultServices);
	}
	
	/**
	 * @param array
	 */
	protected function loadServices(array $config)
	{
		foreach ($config as $name => $data) {
			$service = key_exists('class', $data) ? $data['class'] : (key_exists('factory', $data) ? $data['factory'] : NULL);
			
			$this->getContext()->addService($name, $service, key_exists('singleton', $data) ? $data['singleton'] : TRUE, $data);
			
			if (key_exists('run', $data) && $data['run']) {
				$this->autoRunServices[] = $name;
			}
		}
	}
	
	/**
	 * @param Config
	 */
	protected function loadConstants(Config $config)
	{
		foreach ($config as $key => $value) {
			define($key, $value);
		}
	}
	
	/**
	 * @param Config
	 */
	protected function loadModes(Config $config)
	{
		foreach($config as $mode => $state) {
			Environment::setMode($mode, $state);
		}
	}
	
	protected function autoRunServices()
	{
		foreach ($this->autoRunServices as $service) {
			$this->getContext()->getService($service);
		}
	}
	
	/**
	 * Loads global configuration from file and process it.
	 * @param  string|\Nette\Config\Config  file name or Config object
	 * @return \Nette\Config\Config
	 * 
	 * @author Patrik Votoček
	 */
	public function loadConfig($file)
	{
		$this->onBeforeLoad();
		
		$environment = Environment::getName(); // BACK compatability
		$this->loadEnvironmentName($environment);
		
		if ($file instanceof Config) {
			$config = $file;
			$file = NULL;
		} else {
			if ($file === NULL) {
				$file = $this->defaultConfigFile;
			}
			$file = Environment::expand($file);
			$config = Config::fromFile($file, $environment);
		}
		
		if (isset($config->php)) {
			$this->loadIni($config->php);
		}
		
		$this->loadParameters($config);
		$this->loadDefaultServices();
		
		if (isset($config->services)) {
			$this->loadServices($config->services->toArray());
		}
		
		if (isset($config->const)) {
			$this->loadConstants($config->const);
		}
		if (isset($config->mode)) {
			$this->loadModes($config->mode);
		}
		
		$this->autoRunServices();
		
		$this->onAfterLoad();
		
		return $config;
	}
	
	/******************************************** FACTORIES **************************************************/
	
	public $defaultServices = array(
		'Nette\Application\Application' => array('factory' => array(__CLASS__, 'createApplication')),
		'Nette\Web\HttpContext' => array('class' => 'Nette\Web\HttpContext'),
		'Nette\Web\IHttpRequest' => array('factory' => array(__CLASS__, 'createHttpRequest')),
		'Nette\Web\IHttpResponse' => array('class' => 'Nette\Web\HttpResponse'),
		'Nette\Web\IUser' => array('class' => 'Nette\Web\User'),
		'Nette\Caching\ICacheStorage' => array('factory' => array(__CLASS__, 'createCacheStorage')),
		'Nette\Caching\ICacheJournal' => array('factory' => array(__CLASS__, 'createCacheJournal')),
		'Nette\Mail\IMailer' => array('factory' => array(__CLASS__, 'createMailer')),
		'Nette\Web\Session' => array('class' => 'Nette\Web\Session'),
		'Nette\Loaders\RobotLoader' => array('factory' => array(__CLASS__, 'createRobotLoader'), 'run' => TRUE),
		'Nella\Registry\GlobalComponentFactories' => array(
			'factory' => array(__CLASS__, 'createRegistryGlobalComponentFactories')
		), 
		'Nella\Registry\NamespacePrefixes' => array('factory' => array(__CLASS__, 'createRegistryNamespacePrefixes')), 
		'Nella\Registry\TemplateDirs' => array('factory' => array(__CLASS__, 'createRegistryTemplateDirs')), 
		'Doctrine\ORM\EntityManager' => array('factory' => array('Nella\Doctrine\ServiceFactory', 'entityManager')), 
		'Doctrine\ORM\Configuration' => array('factory' => array('Nella\Doctrine\ServiceFactory', 'configuration')), 
		'Doctrine\Common\EventManager' => array('class' => 'Doctrine\Common\EventManager'), 
		'Nette\Security\IAuthenticator' => array(
			'class' => 'Nella\Security\Authenticator', 
			'arguments' => array('@Doctrine\ORM\EntityManager')
		), 
		'Nette\Security\IAuthorizator' => array(
			'class' => 'Nella\Security\Authorizator', 
			'arguments' => array('@Doctrine\ORM\EntityManager'), 
		), 
		'Doctrine\Common\Cache\Cache' => array(
			'class' => 'Nella\Doctrine\Cache', 
			'arguments' => array('@Nette\Caching\ICacheStorage'), 
		), 
		'Doctrine\DBAL\Logging\SQLLogger' => array('factory' => 'Nella\Doctrine\Panel::create', 'run' => TRUE), 
		//'Nette\\Templates\\ITemplateFactory' => array(̈́'class' => 'Nette\Templates\TemplateFactory'), 
	);
	
	/**
	 * Get initial instance of context
	 * 
	 * @return \Nella\DependencyInjection\IContext
	 */
	public function createContext()
	{
		$class = $this->contextClass;
		$context = new $class;
		foreach ($this->defaultServices as $name => $service) {
			$context->addService($name, $service);
		}
		
		return $context;
	}
	
	/**
	 * @return \Nella\Application\Application
	 */
	public static function createApplication(array $options = NULL)
	{
		if (Environment::getVariable('baseUri', NULL) === NULL) {
			Environment::setVariable('baseUri', Environment::getHttpRequest()->getUri()->getBaseUri());
		}

		$context = clone Environment::getContext();
		$context->addService('Nette\\Application\\IRouter', 'Nette\Application\MultiRouter');

		if (!$context->hasService('Nette\\Application\\IPresenterFactory')) {
			$context->addService('Nette\\Application\\IPresenterFactory', function() use ($context) {
				return new \Nella\Application\PresenterFactory(Environment::getVariable('appDir'), $context);
			});
		}

		$class = isset($options['class']) ? $options['class'] : 'Nella\Application\Application';
		$application = new $class;
		$application->setContext($context);
		$application->catchExceptions = Environment::isProduction();
		return $application;
	}
	
	/**
	 * @return FreezableArray
	 */
	public static function createRegistryGlobalComponentFactories()
	{
		return new FreezableArray;
	}
	
	/**
	 * @return FreezableArray
	 */
	public static function createRegistryNamespacePrefixes()
	{
		$registry = new FreezableArray;
		$registry['app'] = "App\\";
		$registry['framework'] = "Nella\\";
		return $registry;
	}
	
	/**
	 * @return FreezableArray
	 */
	public static function createRegistryTemplateDirs()
	{
		$registry = new FreezableArray;
		$registry['app'] = APP_DIR;
		$registry['nella'] = NELLA_FRAMEWORK_DIR;
		return $registry;
	}
}
