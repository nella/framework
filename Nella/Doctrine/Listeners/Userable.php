<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine\Listeners;

use Nette\Caching\Cache;

/**
 * Timestampable listenere
 *
 * updating timestamp
 *
 * @author	Patrik Votoček
 */
class Userable extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/** @var \Nella\Security\User */
	private $user;
	/** @var \Nette\Caching\Cache */
	private $cache;

	/**
	 * @param \Nella\Security\User
	 * @param \Nette\Caching\IStorage
	 */
	public function __construct(\Nella\Security\User $user = NULL, \Nette\Caching\IStorage $cacheStorage = NULL)
	{
		$this->user = $user;
		$this->cache = $cacheStorage ? new Cache($cacheStorage, "Nella.Models.Userable") : array();
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
    {
        return array(
        	\Doctrine\ORM\Events::onFlush,
        	\Doctrine\ORM\Events::loadClassMetadata,
        );
    }

    /**
     * @param \Nella\Doctrine\Entity
	 * @return void
     */
    protected function update(\Nella\Models\IEntity $entity)
    {
    	if (($properties = $this->cache->load(get_class($entity))) && ($identity = $this->user->identity)) {
    		foreach ($properties as $property) {
				$ref = new \Nette\Reflection\Property($property[0], $property[1]);
				$ref->setAccessible(TRUE);
				if (!$ref->hasAnnotation('creator') || !$ref->getValue($entity)) {
					$ref->setValue($entity, $identity);
				}
	        }
	    }
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs
	 * @return void
     */
    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $args)
    {
    	$em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            $this->update($entity);
            $class = $em->getClassMetadata(get_Class($entity));
			$uow->computeChangeSet($class, $entity);
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            $this->update($entity);
            $class = $em->getClassMetadata(get_Class($entity));
			$uow->computeChangeSet($class, $entity);
		}
    }

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $args
	 * @return void
     */
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $args)
    {
    	$metadata = $args->getClassMetadata();
		if (!array_key_exists($metadata->name, $this->cache)) {
			$files = $data = array();
			foreach ($metadata->getReflectionProperties() as $prop) {
				$class = $prop->getDeclaringClass();
				$ref = new \Nette\Reflection\Property($class->getName(), $prop->getName());
				if ($ref->hasAnnotation('creator') || $ref->hasAnnotation('editor')) {
					$data[] = array($ref->getDeclaringClass()->getName(), $ref->getName());
				}
				$files[] = $class->getFileName();
			}

			if (count($data) < 1) {
				$data = NULL;
			}

			if ($this->cache instanceof Cache) {
				$this->cache->save($metadata->name, $data, array(
					Cache::FILES => $files,
				));
			} else {
				$this->cache[$metadata->name] = $data;
			}
		}
    }
}