<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Doctrine;

use Doctrine\ORM\Mapping as orm;

/**
 * Basic entity with ID
 *
 * @orm\mappedSuperclass
 *
 * @author	Patrik Votoček
 *
 * @property-read int $id
 */
abstract class Entity extends \Nette\Object
{
	/**
	 * @orm\id
	 * @orm\generatedValue
	 * @orm\column(type="integer")
	 * @var int
	 */
	private $id;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string
	 * @return string
	 */
	protected static function normalizeString($s)
	{
		$s = trim($s);
		return $s === "" ? NULL : $s;
	}

	public function __construct()
	{

	}
}