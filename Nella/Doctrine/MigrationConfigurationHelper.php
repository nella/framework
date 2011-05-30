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
 * Migraion configuration console helper
 *
 * @author  Patrik Votoček
 */
class MigrationConfigurationHelper extends \Symfony\Component\Console\Helper\Helper
{
	/** @var Container */
	protected $container;

	/**
	 * @param Container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Retrieves Doctrine Migration Configuration
	 *
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->container->migrationConfiguration;
	}

	/**
	 * @see Helper
	 */
	public function getName()
	{
		return 'migration-configuration';
	}
}
