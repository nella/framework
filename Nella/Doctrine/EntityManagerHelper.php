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
 * Console helper.
 *
 * @author	Patrik Votoček
 */
class EntityManagerHelper extends \Symfony\Component\Console\Helper\Helper
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
     * Retrieves Doctrine ORM EntityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->getEntityManager();
    }

    /**
     * @see Helper
     */
    public function getName()
    {
        return 'entityManager';
    }
}
