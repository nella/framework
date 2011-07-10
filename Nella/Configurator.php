<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella;

use Nette\Application\UI\Presenter,
	Nette\DI\Container,
	Nette\Environment;

/**
 * Environment configurator.
 *
 * @author	Patrik Votocek
 */
class Configurator extends \Nette\Configurator
{
	/** @var array */
	public $onBeforeLoadConfig = array();
	/** @var array */
	public $onAfterLoadConfig = array();

	/**
	 * @param string
	 * @param array
	 */
	public function __construct($containerClass = 'Nette\DI\Container', array $params = array())
	{
		parent::__construct($containerClass);

		$container = $this->getContainer();
		$container->params += $params;

		// Back compatability
		Environment::setConfigurator($this);
		Environment::setContext($container);

		// Nella X-Powered
		@header("X-Powered-By: Nette Framework with Nella"); // @ - headers may have been sent

		// Upload dir (tmp files - Mupltiple File Uploader)
		$container->params['uploadDir'] = $container->expand("%tempDir%/uploads");

		// File storage dirs (upoaded images and other files)
		if (defined('STORAGE_DIR')) {
			$container->params['storageDir'] = realpath(STORAGE_DIR);
		} else {
			$container->params['storageDir'] = $container->expand("%appDir%/storage");
		}
		
		// Set file upload temp dir
		ini_set('upload_tmp_dir', $container->params['uploadDir']);
		// Set session dir
		ini_set('session.save_path', $container->expand("%tempDir%/sessions"));

		// Init multilple file upload listener
		Forms\Controls\MultipleFileUpload::register(
			$container->httpRequest,
			$container->expand($container->params['uploadDir'])
		);

		// Namespace prefixes
		$container->params['namespaces'] = array(0 => 'App', 9 => 'Nella');
		// Templates dirs (application parts dirs)
		$container->params['templates'] = array(0 => $container->params['appDir'], 9 => NELLA_FRAMEWORK_DIR);
		// Flash message types
		$container->params['flashes'] = array(
			'success' => "success",
			'error' => "error",
			'info' => "info",
			'warning' => "warning",
		);

		$this->onAfterLoadConfig[] = function(Container $container) {
			// Load panels
			if (!$container->params['consoleMode'] && !$container->params['productionMode']) {
				$container->callbackPanel;
				$container->versionPanel;
				$container->translatorPanel;
				$container->debugPanel;
				$container->userPanel;
			}
		};
	}

	/**
 	 * Loads configuration from file and process it.
 	 * @param \Nette\DI\Container
 	 * @return void
 	 */
 	public function loadConfig($file, $section = NULL)
 	{
 		$this->onBeforeLoadConfig($this->getContainer());
 		$container = parent::loadConfig($file, $section);
		$this->onAfterLoadConfig($this->getContainer());
		return $container;
 	}
	
	/**
	 * @param \Nette\DI\Container
	 * @param array
	 * @return Nette\Application\Application
	 */
	public static function createServiceApplication(Container $container, array $options = NULL)
	{
		$context = new Container;
		$context->addService('httpRequest', $container->httpRequest);
		$context->addService('httpResponse', $container->httpResponse);
		$context->addService('session', $container->session);
		$context->addService('presenterFactory', $container->presenterFactory);
		$context->addService('router', $container->router);
		$context->addService('console', function() use($container) {
			return $container->console;
		});

		Presenter::$invalidLinkMode = $container->params['productionMode']
			? Presenter::INVALID_LINK_SILENT : Presenter::INVALID_LINK_WARNING;

		$class = isset($options['class']) ? $options['class'] : 'Nella\Application\Application';
		$application = new $class($context);
		$application->catchExceptions = $container->params['productionMode'];
		if ($container->session->exists()) {
			$application->onStartup[] = function() use ($container) {
				$container->session->start(); // opens already started session
			};
		}
		return $application;
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Latte\Engine
	 */
	public static function createServiceLatteEngine(Container $container)
	{
		return new Latte\Engine;
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Nella\Application\IPresenterFactory
	 */
	public static function createServicePresenterFactory(Container $container)
	{
		return new Application\PresenterFactory($container);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Symfony\Component\Console\Helper\HelperSet
	 */
	public static function createServiceConsoleHelpers(Container $container)
	{
		$helperSet = new \Symfony\Component\Console\Helper\HelperSet;
		$helperSet->set(new DI\ContainerHelper($container), 'container');
		$helperSet->set(new Doctrine\EntityManagerHelper($container->doctrineContainer), 'em');
		$helperSet->set(new Doctrine\MigrationConfigurationHelper($container->doctrineContainer), 'mc');
    	$helperSet->set(new \Symfony\Component\Console\Helper\DialogHelper(), 'dialog');
		return $helperSet;
	}

	/**
	 * @return array
	 */
	public static function createServiceConsoleCommands()
	{
		return FreezableArray::from(array(
            // DBAL Commands
            new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
            new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),

            // ORM Commands
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
            new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
            new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),

            // Migrations Commands
		    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
		    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
		    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
		    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
		    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
		    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
        ));
	}

	/**
	 * @param \Nette\DI\Container
	 * @return \Symfony\Component\Console\Application
	 */
	public static function createServiceConsole(Container $container)
	{
		$commands = $container->consoleCommands;
        if ($commands instanceof \Nella\FreezableArray) {
        	$commands->freeze();
			$commands = $commands->iterator->getArrayCopy();
        }

        $cli = new \Symfony\Component\Console\Application(
			Framework::NAME . " Command Line Interface", Framework::VERSION
		);
        $cli->setCatchExceptions(FALSE);
        $cli->setHelperSet($container->consoleHelpers);
		$cli->addCommands($commands);
        return $cli;
	}

	/**
	 * @return Application\UI\IComponentContainer
	 */
	public static function createServiceComponents()
	{
		return new Application\UI\ComponentContainer;
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Utils\IActionLogger
	 */
	public static function createServiceActionLogger(Container $container)
	{
		if ($container->hasService('doctrineContainer')) {
			return $container->doctrineContainer->getService('Nella\Utils\LoggerStorages\ActionEntity');
		} else {
			return new Utils\LoggerStorages\FileStorage;
		}
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Doctrine\Container
	 */
	public static function createServiceDoctrineContainer(Container $container)
	{
		return Doctrine\Container::create($container);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Validator\IValidator
	 */
	public static function createServiceValidator(Container $container)
	{
		$classMetadataFactory = new Validator\ClassMetadataFactory($container->cacheStorage);
		$classMetadataFactory->addParser(new Validator\MetadataParsers\Annotation);
		$classMetadataFactory->addParser(new Validator\MetadataParsers\DoctrineEntity($container->doctrineContainer));

		return new Validator\Validator($classMetadataFactory);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Panels\Version
	 */
	public static function createServiceVersionPanel(Container $container)
	{
		return new Panels\Version($container->cacheStorage);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Panels\Callback
	 */
	public static function createServiceCallbackPanel(Container $container)
	{
		return new \Nella\Diagnostics\CallbackBarPanel($container);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Localization\ITranslator
	 */
	public static function createServiceTranslator(Container $container)
	{
		$translator = new Localization\Translator;
		$translator->addDictionary('Nella', NELLA_FRAMEWORK_DIR);
		return $translator;
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Localization\Panel
	 */
	public static function createServiceTranslatorPanel(Container $container)
	{
		return new Localization\Panel($container);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Security\Panel
	 */
	public static function createServiceUserPanel(Container $container)
	{
		return new Security\Panel($container->user->identity, $container->doctrineContainer);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Panels\Debug
	 */
	public static function createServiceDebugPanel(Container $container)
	{
		return new \Nella\Diagnostics\DebugBarPanel($container);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Security\User
	 */
	public static function createServiceUser(Container $container)
	{
		$context = new \Nette\DI\Container;
		$context->addService('authenticator', function() use ($container) {
			return $container->authenticator;
		});
		$context->addService('authorizator', function() use ($container) {
			return $container->authorizator;
		});
		$context->addService('session', $container->session);
		$context->addService('doctrineContainer', function() use ($container) {
			return $container->doctrineContainer;
		});
		return new Security\User($context);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Security\Authenticator
	 */
	public static function createServiceAuthenticator(Container $container)
	{
		return new Security\Authenticator($container->doctrineContainer);
	}

	/**
	 * @param \Nette\DI\Container
	 * @return Security\Authorizator
	 */
	public static function createServiceAuthorizator(Container $container)
	{
		return new Security\Authorizator($container->doctrineContainer);
	}
}
