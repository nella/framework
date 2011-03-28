<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Models;

use Nette\Caching\Cache, 
	Nette\Reflection\PropertyReflection;

/**
 * Timestampable listenere
 * 
 * updating timestamp
 *
 * @author	Patrik Votoček
 */
class TimestampableListener extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/** @var \Nette\Caching\Cache */
	private $cache;
	
	/**
	 * @param \Nette\Caching\ICacheStorage
	 */
	public function __construct(\Nette\Caching\ICacheStorage $storage = NULL)
	{
		$this->cache = $storage ? new Cache($storage, "Nella.Models.Timestampable") : array();
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
     */
    protected function update(&$entity)
    {
		if (array_key_exists(get_class($entity), $this->cache) && is_array($this->cache[get_class($entity)])) {
            foreach ($this->cache[get_class($entity)] as $ref) {
				$ref->setAccessible(TRUE);
				$ref->setValue($entity, new \DateTime);
            }
        }
    }
    
    /**
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs
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
     */
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $args)
    {
    	$metadata = $args->getClassMetadata();
		if (!array_key_exists($metadata->name, $this->cache)) {
			$files = $data = array();
			foreach ($metadata->getReflectionProperties() as $prop) {
				$class = $prop->getDeclaringClass();
				$ref = new \Nette\Reflection\PropertyReflection($class->getName(), $prop->getName());
				if ($ref->hasAnnotation('timestampable')) {
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
}