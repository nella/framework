<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
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
        return array(\Doctrine\ODM\MongoDB\Events::preUpdate);
    }
    
    /**
     * @param Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs
     */
    public function preUpdate(\Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs $args)
    {
    	$dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        foreach ($uow->getScheduledDocumentInsertions() AS $document) {
            if ($document instanceof ITimestampable) {
                $document->updateTimestamps();
            }
        }
        
        foreach ($uow->getScheduledDocumentUpdates() AS $document) {
            if ($document instanceof ITimestampable) {
                $document->updateTimestamps();
            }
        }
    }
}