<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella;

use Nette\Environment, 
	Nette\Config\Config;

/**
 * Nette\Environment helper.
 *
 * @author	Patrik Votoček
 */
class Configurator extends \Nette\Configurator
{
	/** @var array */
	public $defaultServices = array(
		'Nette\\Application\\Application' => array(__CLASS__, 'createApplication'),
		'Nette\\Web\\HttpContext' => 'Nette\Web\HttpContext',
		'Nette\\Web\\IHttpRequest' => array(__CLASS__, 'createHttpRequest'),
		'Nette\\Web\\IHttpResponse' => 'Nette\Web\HttpResponse',
		'Nette\\Web\\IUser' => 'Nette\Web\User',
		'Nette\\Caching\\ICacheStorage' => array(__CLASS__, 'createCacheStorage'),
		'Nette\\Caching\\ICacheJournal' => array(__CLASS__, 'createCacheJournal'),
		'Nette\\Mail\\IMailer' => array(__CLASS__, 'createMailer'),
		'Nette\\Web\\Session' => 'Nette\Web\Session',
		'Nette\\Loaders\\RobotLoader' => array(__CLASS__, 'createRobotLoader'),
		'Nella\\Registry\\GlobalComponentFactories' => array(__CLASS__, 'createRegistryGlobalComponentFactories'), 
		//'Nette\\Templates\\ITemplateFactory' => 'Nette\Templates\TemplateFactory',
	);
	
	/**
	 * Loads global configuration from file and process it.
	 * @param  string|\Nette\Config\Config  file name or Config object
	 * @return \Nette\Config\Config
	 * 
	 * @author Patrik Votoček
	 * @author David Grudl
	 */
	public function loadConfig($file)
	{
		$name = Environment::getName();

		if ($file instanceof Config) {
			$config = $file;
			$file = NULL;

		} else {
			if ($file === NULL) {
				$file = $this->defaultConfigFile;
			}
			$file = Environment::expand($file);
			$config = Config::fromFile($file, $name);
		}

		// process environment variables
		if ($config->variable instanceof Config) {
			foreach ($config->variable as $key => $value) {
				Environment::setVariable($key, $value);
			}
		}

		// expand variables
		$iterator = new \RecursiveIteratorIterator($config);
		foreach ($iterator as $key => $value) {
			$tmp = $iterator->getDepth() ? $iterator->getSubIterator($iterator->getDepth() - 1)->current() : $config;
			$tmp[$key] = Environment::expand($value);
		}

		// process services
		$runServices = array();
		$context = Environment::getContext();
		if ($config->service instanceof Config) {
			foreach ($config->service as $key => $value) {
				$key = strtr($key, '-', '\\'); // limited INI chars
				$options = array();
				$singleton = isset($value->singleton) ? $value->singleton : TRUE;
				
				if (isset($value->autowire)) {
					$options['autowire'] = $value->autowire;
				}
				if (isset($value->argument)) {
					$options['arguments'] = (array) $value->argument;
				}
				if (isset($value->callMethod)) {
					$options['callMethod'] = array();
					foreach ((array) $value->callMethod as $method => $args) {
						$options['callMethod'][$method] = (array) $args;
					}
				}
				
				if (is_string($value)) { // Deprecated ?
					$context->removeService($key);
					$context->addService($key, $value, $singleton, $options);
				} elseif (isset($value->class)) {
					$context->removeService($key);
					$context->addService($key, $value->class, $singleton, $options);
				} else {
					$factory = $value->factory ? $value->factory : (isset($this->defaultServices[$key]) ? $this->defaultServices[$key] : NULL);
					if ($factory) {
						$context->removeService($key);
						$context->addService($key, $factory, $singleton, $options);
					} else {
						throw new \InvalidStateException("Factory method is not specified for service $key.");
					}
					if ($value->run) {
						$runServices[] = $key;
					}
				}
				
				if (isset($value->alias)) {
					$aliases = (is_array($value->alias) || $value->alias instanceof Config) ? $value->alias : array($value->alias);
					foreach ($aliases as $alias) {
						$context->addAlias($alias, $key);
					}
				}
			}
		}

		// process ini settings
		if (!$config->php) { // backcompatibility
			$config->php = $config->set;
			unset($config->set);
		}

		if ($config->php instanceof Config) {
			if (PATH_SEPARATOR !== ';' && isset($config->php->include_path)) {
				$config->php->include_path = str_replace(';', PATH_SEPARATOR, $config->php->include_path);
			}

			foreach (clone $config->php as $key => $value) { // flatten INI dots
				if ($value instanceof Config) {
					unset($config->php->$key);
					foreach ($value as $k => $v) {
						$config->php->{"$key.$k"} = $v;
					}
				}
			}

			foreach ($config->php as $key => $value) {
				$key = strtr($key, '-', '.'); // backcompatibility

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

		// define constants
		if ($config->const instanceof Config) {
			foreach ($config->const as $key => $value) {
				define($key, $value);
			}
		}

		// set modes
		if (isset($config->mode)) {
			foreach($config->mode as $mode => $state) {
				Environment::setMode($mode, $state);
			}
		}

		// auto-start services
		foreach ($runServices as $name) {
			$context->getService($name);
		}

		return $config;
	}
	
	/**
	 * Get initial instance of context
	 * 
	 * @return \Nette\IContext
	 */
	public function createContext()
	{
		$context = new DependencyInjection\Context;
		foreach ($this->defaultServices as $name => $service) {
			$context->addService($name, $service);
		}
		return $context;
	}
	
	/**
	 * @return \Nette\Application\Application
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
				return new Application\PresenterFactory(Environment::getVariable('appDir'), $context);
			});
		}

		$class = isset($options['class']) ? $options['class'] : 'Nette\Application\Application';
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
}
