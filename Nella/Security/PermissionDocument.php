<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Security;

/**
 * Persmission document
 *
 * @author	Patrik Votoček
 * 
 * @embeddedDocument
 */
class PersmissionDocument extends \Nella\Object
{
	/**
	 * @string
	 * @var string
	 */
	private $resource;
	/**
	 * @string
	 * @var string
	 */
	private $privilege;
	
	/**
	 * @return string
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @param string
	 * @return PersmissionDocument
	 */
	public function setResource($resource)
	{
		$resource = trim($resource);
		$this->resource = $resource == "" ? NULL : $resource;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrivilege()
	{
		return $this->privilege;
	}

	/**
	 * @param string
	 * @return PersmissionDocument
	 */
	public function setPrivilege($privilege)
	{
		$privilege = trim($privilege);
		$this->privilege = $privilege == "" ? NULL : $privilege;
		return $this;
	}
}