<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Media\Model;

/**
 * Media listener
 *
 * @author	Patrik Votoček
 */
class Listener extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			\Doctrine\ORM\Events::onFlush,
		);
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs
     */
    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $args)
    {
    	$uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
			$this->processOnFlush($entity);
        }
        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
			$this->processOnFlush($entity);
        }
        foreach ($uow->getScheduledEntityDeletions() AS $entity) {
			$this->processOnFlush($entity);
        }
    }

    /**
     * @param \Nella\Doctrine\Entity
     */
    protected function processOnFlush($entity)
    {
    	if ($entity instanceof BaseFileEntity || $entity instanceof ImageFormatEntity) {
			$entity->onFlush($entity);
		}
    }
}
