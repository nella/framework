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
	Nette\DI\IContainer;

/**
 * Environment configurator.
 * 
 * @author	Patrik Votocek
 */
class Configurator extends \Nette\Configurator
{	
	/** @var array */
	public $onCreateContainer = array();
	
	/** @var array */
	public $defaultServices = array(
		'Nette\\Application\\Application' => array(__CLASS__, 'createApplication'),
		'Nette\\Application\\IPresenterFactory' => array(__CLASS__, 'createPresenterFactory'),
		'Nette\\Web\\HttpContext' => array(__CLASS__, 'createHttpContext'),
		'Nette\\Web\\IHttpRequest' => array(__CLASS__, 'createHttpRequest'),
		'Nette\\Web\\IHttpResponse' => 'Nette\Http\Response',
		'Nette\\Web\\IUser' => array(__CLASS__, 'createHttpUser'),
		'Nette\\Caching\\ICacheStorage' => array(__CLASS__, 'createCacheStorage'),
		'Nette\\Caching\\ICacheJournal' => array(__CLASS__, 'createCacheJournal'),
		'Nette\\Mail\\IMailer' => array(__CLASS__, 'createMailer'),
		'Nette\\Web\\Session' => array(__CLASS__, 'createHttpSession'),
		'Nette\\Loaders\\RobotLoader' => array(__CLASS__, 'createRobotLoader'),
		'templateCacheStorage' => array(__CLASS__, 'createTemplateCacheStorage'), 
		'Nette\Caching\IStorage' => array(__CLASS__, 'createCacheStorageAlias'), 
		'macros' => array(__CLASS__, 'createServiceMacros'), 
		'consoleHelpers' => array(__CLASS__, 'createConsoleHelpers'), 
		'consoleCommands' => array(__CLASS__, 'createConsoleCommands'), 
		'console' => array(__CLASS__, 'createConsole'), 
		'components' => array(__CLASS__, 'createComponents'), 
		'actionLogger' => array(__CLASS__, 'createActionLogger'), 
		'doctrineContainer' => array(__CLASS__, 'createDoctrineContainer'), 
		'validator' => array(__CLASS__, 'createValidator'), 
		'versionPanel' => array(__CLASS__, 'createVersionPanel'), 
		'callbackPanel' => array(__CLASS__, 'createCallbackPanel'), 
	);
	
	/**
	 * Get initial instance of context
	 * 
	 * @return IContainer
	 */
	public function createContainer()
	{
		$container = parent::createContainer();

		defined('TEMP_DIR') && $container->setParam('uploadDir', TEMP_DIR . "/uploaded"); // temporary files dir (uploader)
		defined('STORAGE_DIR') && $container->setParam('storageDir', STORAGE_DIR); // file storage
		defined('IMAGE_CACHE_DIR') && $container->setParam('imageCacheDir', IMAGE_CACHE_DIR); // public image cache
		
		$this->onCreateContainer($container);

		return $container;
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Application\Application
	 */
	public static function createApplication(IContainer $container, array $options = NULL)
	{
		$context = new \Nette\DI\Container;
		$context->addService('httpRequest', $container->getService('Nette\\Web\\IHttpRequest'));
		$context->addService('httpResponse', $container->getService('Nette\\Web\\IHttpResponse'));
		$context->addService('session', $container->getService('Nette\\Web\\Session'));
		$context->addService('presenterFactory', $container->getService('Nette\\Application\\IPresenterFactory'));
		$context->addService('router', 'Nette\Application\Routers\RouteList');
		$context->addService('console', $container->getService('console'));

		Presenter::$invalidLinkMode = $container->getParam('productionMode')
			? Presenter::INVALID_LINK_SILENT : Presenter::INVALID_LINK_WARNING;

		$application = new \Nella\Application\Application($context);
		$application->catchExceptions = $container->getParam('productionMode');
		return $application;
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nette\Application\IPresenterFactory
	 */
	public static function createPresenterFactory(IContainer $container)
	{
		return new \Nella\Application\PresenterFactory($container);
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nette\Caching\IStorage
	 */
	public static function createCacheStorageAlias(IContainer $container)
	{
		return $container->getService('Nette\Caching\ICacheStorage');
	}
	
	/**
	 * @return \Nette\Latte\DefaultMacros
	 */
	public static function createServiceMacros()
	{
		return new \Nella\Latte\Macros;
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Symfony\Component\Console\Helper\HelperSet
	 */
	public static function createConsoleHelpers(IContainer $container)
	{
		$helperSet = new \Symfony\Component\Console\Helper\HelperSet;
		$helperSet->set(new DI\ContainerHelper($container), 'container');
		$helperSet->set(new Doctrine\EntityManagerHelper($container->getService('doctrineContainer')), 'em');
		return $helperSet;
	}
	
	/**
	 * @return array
	 */
	public static function createConsoleCommands()
	{
		return new \Nella\FreezableArray(array(
            // DBAL Commands
            new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
            new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),

            // ORM Commands
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
            new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
            new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
            new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),
        ));
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Symfony\Component\Console\Application
	 */
	public static function createConsole(IContainer $container)
	{
		$cli = new \Symfony\Component\Console\Application(
			\Nella\Framework::NAME . " Command Line Interface", \Nella\Framework::VERSION
		);
        $cli->setCatchExceptions(true);
        $cli->setHelperSet($container->getService('consoleHelpers'));
		$commands = $container->getService('consoleCommands');
		$commands->freeze();
        $cli->addCommands($commands->iterator->getArrayCopy());
        return $cli;
	}
	
	/**
	 * @return \Nella\Application\UI\IComponentContainer
	 */
	public static function createComponents()
	{
		return new \Nella\Application\UI\ComponentContainer;
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Utils\IActionLogger
	 */
	public static function createActionLogger(IContainer $container)
	{
		if ($container->hasService('doctrineContainer')) {
			return $container->getService('doctrineContainer')
				->getEntityService('Nella\Utils\LoggerStorages\ActionEntity');
		} else {
			return new \Nella\Utils\LoggerStorages\FileStorage;
		}
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Doctrine\Container
	 */
	public static function createDoctrineContainer(IContainer $container)
	{
		$dc = new \Nella\Doctrine\Container;
		$dc->addService('container', $container);
		$dc->setParam('listeners', array(
			'timestampableListener', 
			'userableListener', 
			'versionListener', 
			'validatorListener', 
		));
		return $dc;
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Validator\IValidator
	 */
	public static function createValidator(IContainer $container)
	{
		$cacheStorage = $container->getService('Nette\Caching\ICacheStorage');
		
		$classMetadataFactory = new \Nella\Validator\ClassMetadataFactory($cacheStorage);
		$classMetadataFactory->addParser(new \Nella\Validator\MetadataParsers\Annotation);
		/*$dc = $container->getService('doctrineContainer');
		$classMetadataFactory->addParser(new \Nella\Validator\MetadataParsers\Doctrine($dc));*/
		
		return new \Nella\Validator\Validator($classMetadataFactory);
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Panels\Version
	 */
	public static function createVersionPanel(IContainer $container)
	{
		$cacheStorage = $container->getService('Nette\Caching\ICacheStorage');
		
		return new \Nella\Panels\Version($cacheStorage);
	}
	
	/**
	 * @param \Nette\DI\IContainer
	 * @return \Nella\Panels\Callback
	 */
	public static function createCallbackPanel(IContainer $container)
	{
		return new \Nella\Panels\Callback($container);
	}
}
