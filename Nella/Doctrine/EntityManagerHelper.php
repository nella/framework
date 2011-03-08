<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine;

use Nette\Environment, 
	Nette\Config\Config;

/**
 * Nette\Environment helper.
 *
 * @author	Patrik Votoček
 */
class EntityManagerHelper extends \Symfony\Component\Console\Helper\Helper
{
	/**
     * Doctrine ORM EntityManager lazy loader
     * @var Closuer
     */
    protected $_em;

    /**
     * Constructor
     *
     * @param Closure
     */
    public function __construct($em)
    {
        $this->_em = $em;
    }
	
	/**
     * Retrieves Doctrine ORM EntityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }

    /**
     * @see Helper
     */
    public function getName()
    {
        return 'entityManager';
    }
}
