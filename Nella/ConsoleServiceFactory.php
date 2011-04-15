<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella;

/**
 * Console runner
 *
 * @author	Patrik Votoček
 */
class ConsoleServiceFactory extends \Nette\Object
{
	/**
	 * @param DependencyInjection\IContext
	 * @return \Symfony\Component\Console\Helper\HelperSet
	 */
	public static function createHelperSet(DependencyInjection\IContext $context)
	{
		$helperSet = new \Symfony\Component\Console\Helper\HelperSet();
		$helperSet->set(new DependencyInjection\ContextHelper($context), 'context');
		$helperSet->set(new Doctrine\EntityManagerHelper(function() use ($context) {
			return $context->getService('Doctrine\ORM\EntityManager');
		}), 'em');
		return $helperSet;
	}

	/**
	 * @param \Symfony\Component\Console\Helper\HelperSet
	 * @return \Symfony\Component\Console\Application
	 */
	public static function createApplication(\Symfony\Component\Console\Helper\HelperSet $helperSet)
	{
		$cli = new \Symfony\Component\Console\Application('Doctrine Command Line Interface', \Doctrine\ORM\Version::VERSION);
        $cli->setCatchExceptions(true);
        $cli->setHelperSet($helperSet);
        static::addCommands($cli);
        return $cli;
	}

    /**
     * @param \Symfony\Component\Console\Application $cli
     */
    static public function addCommands(\Symfony\Component\Console\Application $cli)
    {
        $cli->addCommands(array(
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
}