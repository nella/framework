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
 * Versionable listener
 * 
 * making and saving snapshot
 *
 * @author	Patrik Votoček
 */
class VersionListener extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/**
	 * @return array
	 */
	public function getSubscribedEvents()
    {
        return array(\Doctrine\ORM\Events::onFlush);
    }

    /**
     * @param Doctrine\ORM\ORM\Event\OnFlushEventArgs
     */
    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            if ($entity instanceof IVersionable) {
                $this->takeSnapshot($em, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            if ($entity instanceof IVersionable) {
                $this->takeSnapshot($em, $entity);
            }
        }
    }

    /**
     * @param \Doctrine\ORM\EntityManager
     * @param IVersionable
     */
    private function takeSnapshot(\Doctrine\ORM\EntityManager $em, IVersionable $entity)
    {
        $version = new VersionEntity($entity);
        $class = $em->getClassMetadata(get_class($version));

        $em->persist($version);
        $em->getUnitOfWork()->computeChangeSet($class, $version);
    }
}