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
class ContextBuilder extends \Nette\DI\Configurator
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
	 * @throws \Nette\InvalidArgumentException
	 */
	public function setContextClass($class)
	{
		if (!class_exists($class)) {
			throw new \Nette\InvalidArgumentException("Context class '$class' does not exist");
		}
		$ref = new \Nette\Reflection\ClassType($class);
		if (!$ref->implementsInterface('Nella\DependencyInjection\IContext')) {
			throw new \Nette\InvalidArgumentException("Context class '$class' is not an implementor of 'Nella\DependencyInjection\IContext' interface");
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
	 * @throws \Nette\NotSupportedException
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
				throw new \Nette\InvalidStateException("Configuration value for directive '$key' is not scalar.");
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
							throw new \Nette\NotSupportedException('Required function ini_set() is disabled.');
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
		'Nette\Http\Context' => array(
			'class' => 'Nette\Http\Context', 
			'aliases' => array('Nette\Web\HttpContext'), 
		),
		'Nette\Http\IRequest' => array(
			'factory' => array(__CLASS__, 'createHttpRequest'), 
			'aliases' => array('Nette\Web\IHttpRequest'), 
		),
		'Nette\Http\IResponse' => array(
			'class' => 'Nette\Http\Response', 
			'aliases' => array('Nette\Web\IHttpResponse'), 
		),
		'Nette\Http\IUser' => array(
			'class' => 'Nette\Http\User', 
			'aliases' => array('Nette\Web\IUser'), 
		),
		'Nette\Caching\IStorage' => array(
			'factory' => array(__CLASS__, 'createCacheStorage'), 
			'aliases' => array('Nette\Caching\ICacheStorage'), 
		),
		'Nette\Caching\Storages\IJournal' => array(
			'factory' => array(__CLASS__, 'createCacheJournal'), 
			'aliases' => array('Nette\Caching\ICacheJournal'), 
		),
		'Nette\Mail\IMailer' => array('factory' => array(__CLASS__, 'createMailer')),
		'Nette\Http\Session' => array(
			'class' => 'Nette\Http\Session', 
			'aliases' => array('Nette\Web\Session'), 
		),
		'Nette\Loaders\RobotLoader' => array('factory' => array(__CLASS__, 'createRobotLoader'), 'run' => TRUE),
		'Nette\Latte\DefaultMacros' => array('class' => 'Nella\Latte\Macros'),
		'Nette\Latte\Engine' => array(
			'class' => 'Nette\Latte\Engine',
			'methods' => array(
				array('method' => "setHandler", 'arguments' => array('@Nette\Latte\DefaultMacros')),
			),
		),
		'Nella\Registry\GlobalComponentFactories' => array(
			'factory' => array(__CLASS__, 'createRegistryGlobalComponentFactories')
		),
		'Nella\Registry\NamespacePrefixes' => array('factory' => array(__CLASS__, 'createRegistryNamespacePrefixes')),
		'Nella\Registry\TemplateDirs' => array('factory' => array(__CLASS__, 'createRegistryTemplateDirs')),
		'Doctrine\ORM\EntityManager' => array('factory' => array('Nella\Doctrine\ServiceFactory', 'entityManager')),
		'Doctrine\ORM\Configuration' => array('factory' => array('Nella\Doctrine\ServiceFactory', 'configuration')),
		'Doctrine\Common\EventManager' => array(
			'class' => 'Doctrine\Common\EventManager',
			'methods' => array(
				array('method' => "addEventSubscriber", 'arguments' => array('@Nella\Models\VersionListener'),
				array('method' => "addEventSubscriber", 'arguments' => array('@Nella\Models\TimestampableListener')),
				array('method' => "addEventSubscriber", 'arguments' => array('@Nella\Models\UserableListener')),
				array('method' => "addEventSubscriber", 'arguments' => array('@Nella\Models\ValidatorListener')),
				),
			),
		),
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
			'arguments' => array('@Nette\Caching\IStorage'),
		),
		'Doctrine\DBAL\Logging\SQLLogger' => array('factory' => 'Nella\Doctrine\Panel::create', 'run' => TRUE),
		'Nella\Validator\IClassMetadataFactory' => array(
			'class' => 'Nella\Validator\ClassMetadataFactory',
			'arguments' => array('@Nette\Caching\IStorage'),
		),
		'Nella\Validator\IValidator' => array(
			'class' => 'Nella\Validator\Validator',
			'arguments' => array('@Nella\Validator\IClassMetadataFactory'),
		),
		'Nella\Models\VersionListener' => array('class' => 'Nella\Models\VersionListener'),
		'Nella\Models\TimestampableListener' => array('class' => 'Nella\Models\TimestampableListener'),
		'Nella\Models\UserableListener' => array(
			'factory' => 'Nella\Models\UserableListener::getInstance',
			'arguments' => array('@Nette\Http\IUser'),
		),
		'Nella\Models\ValidatorListener' => array(
			'class' => 'Nella\Models\ValidatorListener',
			'arguments' => array('@Nella\Validator\IValidator'),
		),
		'Symfony\Component\Console\Helper\HelperSet' => array(
			'factory' => 'Nella\ConsoleServiceFactory::createHelperSet',
			'arguments' => array('@Nella\DependencyInjection\IContext'),

		),
		'Symfony\Component\Console\Application' => array(
			'factory' => 'Nella\ConsoleServiceFactory::createApplication',
			'arguments' => array('@Symfony\Component\Console\Helper\HelperSet'),
		),
		//'Nette\\Templates\\ITemplateFactory' => array(̈́'class' => 'Nette\Templating\TemplateFactory'),
	);

	/**
	 * Get an initial instance of context
	 *
	 * @return IContext
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
		if (Environment::getVariable('baseUrl', NULL) === NULL) {
			Environment::setVariable('baseUrl', Environment::getHttpRequest()->getUrl()->getBaseUrl());
		}

		$context = clone Environment::getContext();
		$context->addService('Nette\Application\IRouter', 'Nette\Application\Routers\RouteList');

		if (!$context->hasService('Nette\Application\IPresenterFactory')) {
			$context->addService('Nette\Application\IPresenterFactory', function() use ($context) {
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
