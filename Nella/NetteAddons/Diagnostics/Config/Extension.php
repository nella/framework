<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Diagnostics\Config;

use Nette\Config\Configurator;

/**
 * Diagnostics services
 *
 * @author	Patrik Votoček
 */
class Extension extends \Nette\Config\CompilerExtension
{
	/** @var array */
	public $defaults = array(
		'loggerUrl' => 'http://localhost:50921/api/log.json',
		'accessLoggerUrl' => 'http://localhost:50921/api/access.json',
	);

	/**
	 * Processes configuration data
	 *
	 * @throws \Nette\InvalidStateException
	 */
	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		if (!isset($config['appId']) || !isset($config['appSecret'])) {
			return;
		}

		$builder->addDefinition($this->prefix('accessStorage'))
			->setClass('Nella\NetteAddons\Diagnostics\LoggerStorages\Http', array(
				$config['appId'], $config['appSecret'], $config['accessLoggerUrl']
			));
		$builder->addDefinition($this->prefix('accessLogger'))
			->setClass('Nella\NetteAddons\Diagnostics\AccessLogger', array($this->prefix('@accessStorage')));
	}

	/**
	 * @param \Nette\Application\Application
	 * @param \Nette\Http\Response
	 * @param \Nella\NetteAddons\Diagnostics\AccessLogger
	 */
	public static function setCallback(\Nette\Application\Application $application, \Nette\Http\Response $res, \Nella\NetteAddons\Diagnostics\AccessLogger $logger)
	{
		$application->onShutdown[] = function(\Nette\Application\Application $application) use($logger, $res) {
			$logger->log($res);
		};
	}

	/**
	 * @param \Nette\Utils\PhpGenerator\ClassType
	 */
	public function afterCompile(\Nette\Utils\PhpGenerator\ClassType $class)
	{
		$config = $this->getConfig($this->defaults);
		if (!isset($config['appId']) || !isset($config['appSecret'])) {
			return;
		}

		$password = isset($config['password']) ? $config['password'] : FALSE;

		$initialize = $class->methods['initialize'];

		$initialize->addBody('\Nella\NetteAddons\Diagnostics\Logger::register(?, ?, ?, ?);', array(
			$config['appId'], $config['appSecret'], $password, $config['loggerUrl']
		));

		$initialize->addBody(get_called_class().'::setCallback($this->getService(?), $this->getService(?), $this->getService(?));', array(
			'application', 'httpResponse', $this->prefix('accessLogger')
		));
	}

	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = 'diagnostics')
	{
		$class = get_called_class();
		$configurator->onCompile[] = function(Configurator $configurator, \Nette\Config\Compiler $compiler) use($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}
