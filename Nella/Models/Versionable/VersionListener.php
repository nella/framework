<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Models;

use Doctrine\ORM\Event\OnFlushEventArgs,
    Doctrine\ORM\EntityManager;

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
        return array(\Doctrine\ODM\MongoDB\Events::onFlush);
    }

    /**
     * @param Doctrine\ODM\MongoDB\Event\OnFlushEventArgs
     */
    public function onFlush(\Doctrine\ODM\MongoDB\Event\OnFlushEventArgs $args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        foreach ($uow->getScheduledDocumentInsertions() AS $document) {
            if ($document instanceof IVersionable) {
                $this->takeSnapshot($dm, $document);
            }
        }

        foreach ($uow->getScheduledDocumentUpdates() AS $document) {
            if ($document instanceof IVersionable) {
                $this->takeSnapshot($dm, $document);
            }
        }
    }

    /**
     * @param Doctrine\ODM\MongoDB\DocumentManager
     * @param IVersionable
     */
    private function takeSnapshot(\Doctrine\ODM\MongoDB\DocumentManager $dm, IVersionable $document)
    {
        $version = new VersionDocument($document);
        $class = $dm->getClassMetadata(get_class($document));

        $dm->persist($version);
        $dm->getUnitOfWork()->computeChangeSet($class, $version);
    }
}