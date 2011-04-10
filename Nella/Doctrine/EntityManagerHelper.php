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
	/**
     * Doctrine ORM EntityManager lazy loader
     * @var \Closure
     */
    protected $_em;

    /**
     * Constructor
     *
     * @param \Closure
     */
    public function __construct(\Closure $em)
    {
        $this->_em = $em;
    }

	/**
     * Retrieves Doctrine ORM EntityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return callback($this->_em)->invoke();
    }

    /**
     * @see Helper
     */
    public function getName()
    {
        return 'entityManager';
    }
}
