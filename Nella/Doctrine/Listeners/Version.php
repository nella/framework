<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine\Listeners;

use Nella\Doctrine\IVersionableEntity;

/**
 * Versionable listener
 *
 * Takes and saves a snapshot and deletes it when the source is removed
 *
 * @author	Pavel Kučera
 * @author	Patrik Votoček
 */
class Version extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/**
	 * @return array
	 */
	public function getSubscribedEvents()
    {
        return array(\Doctrine\ORM\Events::postPersist, \Doctrine\ORM\Events::onFlush);
    }

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs
	 * @return void
	 */
	public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $args)
	{
		$em = $args->getEntityManager();
		$entity = $args->getEntity();

		if ($entity instanceof IVersionableEntity) {
			$this->takeSnapshot($em, $entity);
			$em->flush();
		}
	}

    /**
     * @param \Doctrine\Common\EventArgs
	 * @return void
     */
    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $args)
	{
		$em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

		// Process updated entities
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof IVersionableEntity) {
                $this->takeSnapshot($em, $entity);
            }
        }

		// Process removed entities
		$sources = array();
		foreach ($uow->getScheduledEntityDeletions() as $entity) {
			if ($entity instanceof IVersionableEntity) {
                $sources[get_class($entity)][] = $entity->getId();
            }
		}
		if ($sources) {
			$qb = $em->createQueryBuilder()
				->delete('Nella\Doctrine\Listeners\VersionEntity', 'v');
			foreach ($sources as $class => $ids) {
				$qb->andWhere('v.entityClass = :class', $qb->expr()->in('v.entityId', $ids))->setParameter('class', $class);
			}
			$qb->getQuery()->execute();
		}
    }

    /**
     * @param \Doctrine\ORM\EntityManager
     * @param \Nella\Doctrine\IVersionableEntity
     */
    private function takeSnapshot(\Doctrine\ORM\EntityManager $em, IVersionableEntity $entity)
    {
        $version = new VersionEntity($entity);
        $class = $em->getClassMetadata(get_class($version));

        $em->persist($version);
        $em->getUnitOfWork()->computeChangeSet($class, $version);
    }
}