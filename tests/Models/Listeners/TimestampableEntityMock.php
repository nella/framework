<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Models\Listeners;

/**
 * @entity
 */
class TimestampableEntityMock extends \Nella\Models\Entity
{
	/**
	 * @timestampable
	 * @column(type="datetime")
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
}
