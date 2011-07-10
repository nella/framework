<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Media;

/**
 * Media listener
 *
 * @author	Patrik Votoček
 */
class Listener extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/** @var string */
	private $fileStorageDir;
	/** @var string */
	private $imageStorageDir;
	/** @var array */
	private $imageMetadatas = array();
	/** @var array */
	private $fileMetadatas = array();
	/** @var array */
	private $imageMap = array();
	/** @var array */
	private $fileMap = array();
	
	/**
	 * @param string
	 * @param string
	 */
	public function __construct($fileStorageDir, $imageStorageDir)
	{
		$this->fileStorageDir = $fileStorageDir;
		$this->imageStorageDir = $imageStorageDir;
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
    {
        return array(
        	\Doctrine\ORM\Events::loadClassMetadata, 
			\Doctrine\ORM\Events::postLoad, 
        );
    }

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $args
     */
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $args)
    {
    	$this->updateDiscriminator($args->getClassMetadata(), $args->getEntityManager());
    }
	
	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs
	 */
	public function postLoad(\Doctrine\ORM\Event\LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if ($entity instanceof \Nella\Media\ImageEntity) {
			$entity->setStorageDir($this->imageStorageDir);
		} elseif ($entity instanceof \Nella\Media\FileEntity) {
			$entity->setStorageDir($this->fileStorageDir);
		}
	}

	/**
	 * Update the discriminator map
	 * @param \Doctrine\ORM\Mapping\ClassMetadata $metadata
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	private function updateDiscriminator(\Doctrine\ORM\Mapping\ClassMetadata $metadata, \Doctrine\ORM\EntityManager $em)
	{
    	if ($metadata->name == 'Nella\Media\FileEntity' || in_array('Nella\Media\FileEntity', $metadata->parentClasses)) {
			$this->fileMetadatas[$metadata->name] = $metadata;
			$metadata->setDiscriminatorMap(array_merge($metadata->discriminatorMap, $this->fileMap));
    	} elseif ($metadata->name == 'Nella\Media\ImageEntity' || in_array('Nella\Media\ImageEntity', $metadata->parentClasses)) {
			$this->imageMetadatas[$metadata->name] = $metadata;
			$metadata->setDiscriminatorMap(array_merge($metadata->discriminatorMap, $this->imageMap));
    	}
	}

	/**
	 * @param string
	 * @param string
	 */
	public function addImage($id, $class)
	{
		if (!class_exists($class)) {
			throw new \Nette\InvalidArgumentException("Class '$class' does not exist");
		}

		foreach ($this->imageMetadatas as $metadata) {
			$metadata->setDiscriminatorMap[$id] = $class;
		}
		
		$this->imageMap[$id] = $class;
	}

	/**
	 * @param string
	 * @param string
	 */
	public function addFile($id, $class)
	{
		if (!class_exists($class)) {
			throw new \Nette\InvalidArgumentException("Class '$class' does not exist");
		}
		
		foreach ($this->fileMetadatas as $metadata) {
			$metadata->setDiscriminatorMap[$id] = $class;
		}

		$this->fileMap[$id] = $class;
	}
}
