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
 * Base model service
 *
 * @author	Patrik Votoček
 * 
 * @property-read \Doctrine\ODM\MongoDB\DocumentManager $documentManager
 */
abstract class Service extends \Nette\Object
{
	/** @var \Doctrine\ODM\MongoDB\DocumentManager */
	private $documentManager;
	/** @var string */
	private $documentName;
	
	/**
	 * @param \Doctrine\ODM\MongoDB\DocumentManager
	 * @param string
	 */
	public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $documentManager, $documentName = NULL)
	{
		$this->documentManager = $documentManager;
		$this->documentName = $documentName;
	}
	
	/**
	 * @return \Doctrine\ODM\MongoDB\DocumentManager
	 */
	public function getDocumentManager()
	{
		return $this->documentManager;
	}
	
	/**
	 * @return string
	 */
	public function getDocumentName()
	{
		return $this->documentName;
	}
	
	/**
	 * @param string
	 * @return \Doctrine\ODM\MongoDB\DocumentRepository
	 * @throws \InvalidArgumentException
	 */
	public function getDocumentRepository($documentName = NULL)
	{
		$documentName = $documentName ?: $this->getDocumentName();
		if (empty($documentName)) {
			throw new \InvalidArgumentException("Default document name not set you must use document name as param");
		}
		
		return $this->getDocumentManager()->getRepository($documentName);
	}
	
	/**
	 * @param string
	 * @return \Doctrine\ODM\MongoDB\Mapping\ClassMetadata
	 * @throws \InvalidArgumentException
	 */
	public function getClassMetadata($documentName = NULL)
	{
		$documentName = $documentName ?: $this->getDocumentName();
		if (empty($documentName)) {
			throw new \InvalidArgumentException("Default document name not set you must use document name as param");
		}
		
		return $this->getDocumentManager()->getClassMetadata($documentName);
	}
	
	/**
	 * @param BaseDocument
	 * @return BaseDocument
	 */
	public function persist(BaseDocument $document)
	{
		$this->getDocumentManager()->persist($document);
		return $document;
	}
	
	/**
	 * @param BaseDocument
	 * @return BaseDocument
	 */
	public function remove(BaseDocument $document)
	{
		$this->getDocumentManager()->remove($document);
		return $document;
	}
	
	public function flush()
	{
		return $this->getDocumentManager()->flush();
	}
}