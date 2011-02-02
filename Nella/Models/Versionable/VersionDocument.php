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
 * Versions storage object for repository
 * 
 * @document(collection="snapshots")
 *
 * @author	Patrik Votoček
 */
class VersionDocument extends Document
{
	/**
	 * @timestamp
	 * @var DateTime
	 */
	private $created;
	/**
	 * @int
	 * @var int
	 */
	private $documentId;
	/**
	 * @string
	 * @var string
	 */
	private $documentData;
	/**
	 * @string
	 * @var string
	 */
	private $documentClass;
	
	/**
	 * @param IVersionable
	 */
	public function __construct(IVersionable $document)
	{
		$this->created = new \DateTime;
		$this->documentId = $document->getId();
		$this->documentData = $document->takeSnapshot();
		$this->documentClass = get_class($document);
	}
	
	/**
	 * @return DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}
	
	/**
	 * @return int
	 */
	public function getDocumentId()
	{
		return $this->documentId;
	}
	
	/**
	 * @return string
	 */
	public function getDocumentData()
	{
		return $this->documentData;
	}
	
	/**
	 * @return string
	 */
	public function getDocumentClass()
	{
		return $this->documentClass;
	}
}
