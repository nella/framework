<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\DI;

/**
 * The dependency injection container helper.
 * 
 * @author	Patrik Votocek
 */
class ContainerHelper extends \Symfony\Component\Console\Helper\Helper
{
	/** @var \Nette\DI\IContainer */
    protected $container;

    /**
     * @param \Nette\DI\IContainer
     */
    public function __construct(\Nette\DI\IContainer $container)
    {
        $this->container = $container;
    }

	/**
     * Retrieves Nella DI Container
     *
     * @return \Nette\DI\IContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @see Helper
     */
    public function getName()
    {
        return 'diContainer';
    }
}