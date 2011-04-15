<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Models;

use Nette\Caching\Cache;

/**
 * Timestampable listenere
 *
 * updating timestamp
 *
 * @author	Patrik Votoček
 */
class UserableListener extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/** @var \Nella\Security\IdentityEntity */
	private $identity;
	/** @var \Nette\Caching\Cache */
	private $cache;

	/**
	 * @param \Nella\Security\IdentityEntity
	 * @param \Nette\Caching\IStorage
	 */
	public function __construct(\Nella\Security\IdentityEntity $identity = NULL, \Nette\Caching\IStorage $cacheStorage = NULL)
	{
		$this->identity = $identity;
		$this->cache = $cacheStorage ? new Cache($cacheStorage, "Nella.Models.Userable") : array();
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
    {
        return array(
        	\Doctrine\ORM\Events::preUpdate,
        	\Doctrine\ORM\Events::loadClassMetadata,
        );
    }

    /**
     * @param BaseEntity
	 * @return void
     */
    protected function update(BaseEntity $entity)
    {
		if (array_key_exists(get_class($entity), $this->cache) && is_array($this->cache[get_class($entity)])) {
	        foreach ($this->cache[get_class($entity)] as $ref) {
				$ref->setAccessible(TRUE);
				if (!$ref->hasAnnotation('creator') || !$ref->getValue($entity)) {
					$ref->setValue($entity, $this->identity);
				}
	        }
	    }
    }

    /**
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs
	 * @return void
     */
    public function preUpdate(\Doctrine\ORM\Event\PreUpdateEventArgs $args)
    {
    	$em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            $this->update($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            $this->update($entity);
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
					$data[] = $ref;
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

    /**
     * @param \Nette\Http\IUser
     * @return UserableListener
     */
    public static function getInstance(\Nette\Http\IUser $user)
    {
    	return new static($user->identity ? $user->identity->entity : NULL);
    }
}