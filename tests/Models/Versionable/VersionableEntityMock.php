<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Models;

/**
 * @entity
 */
class VersionableEntityMock extends \Nella\Models\Entity implements \Nella\Models\IVersionable
{
	/** @column(type="string") */
	private $data;
	
	/**
	 * @return string
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * @param string
	 * @return VersionableEntityMock
	 */
	public function setData($data)
	{
		$data = trim($data);
		$this->data = $data == "" ? NULL : $data;
		return $this;
	}
	
	public function takeSnapshot()
	{
		return serialize(array('data' => $this->data));
	}
}