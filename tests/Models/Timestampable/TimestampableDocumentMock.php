<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Models;

/**
 * @document
 */
class TimestampableDocumentMock extends \Nella\Models\Document implements \Nella\Models\ITimestampable
{
	/**
	 * @datetime
	 * @var DateTime
	 */
	private $datetime = NULL;
	
	public function getDatetime()
	{
		return $this->datetime;
	}
	
	public function setDatetime(\Datetime $datetime)
	{
		$this->datetime = $datetime;
		return $this;
	}
	
	public function updateTimestamps()
	{
		$this->datetime = new \DateTime;
	}
}
