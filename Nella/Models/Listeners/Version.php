<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Models\Listeners;

use Nella\Models\IVersionableEntity;

/**
 * Versionable listener
 *
 * Takes and saves a snapshot
 *
 * @author	Patrik Votoček
 */
class Version extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/**
	 * @return array
	 */
	public function getSubscribedEvents()
    {
        return array(\Doctrine\ORM\Events::postUpdate);
    }

    /**
     * @param \Doctrine\Common\EventArgs
	 * @return void
     */
    public function postUpdate(\Doctrine\Common\EventArgs $args)
    {
    	$em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            if ($entity instanceof IVersionableEntity) {
                $this->takeSnapshot($em, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            if ($entity instanceof IVersionableEntity) {
                $this->takeSnapshot($em, $entity);
            }
        }
        $em->flush();
    }

    /**
     * @param \Doctrine\ORM\EntityManager
     * @param \Nella\Models\IVersionableEntity
     */
    private function takeSnapshot(\Doctrine\ORM\EntityManager $em, IVersionableEntity $entity)
    {
        $version = new VersionEntity($entity);
        $class = $em->getClassMetadata(get_class($version));

        $em->persist($version);
        $em->getUnitOfWork()->computeChangeSet($class, $version);
    }
}