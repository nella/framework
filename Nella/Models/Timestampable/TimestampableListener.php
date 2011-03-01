<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Models;

/**
 * Timestampable listenere
 * 
 * updating timestamp
 *
 * @author	Patrik Votoček
 */
class TimestampableListener extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/**
	 * @return array
	 */
	public function getSubscribedEvents()
    {
        return array(\Doctrine\ORM\Events::preUpdate);
    }
    
    /**
     * @param Doctrine\ORM\Event\PreUpdateEventArgs
     */
    public function preUpdate(\Doctrine\ORM\Event\PreUpdateEventArgs $args)
    {
    	$em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            if ($entity instanceof ITimestampable) {
                $entity->updateTimestamps();
            }
        }
        
        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            if ($entity instanceof ITimestampable) {
                $entity->updateTimestamps();
            }
        }
    }
}