<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
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
	protected static function normalizeString($input)
	{
		$input = trim($input);
		return $input === '' ? NULL : $input;
	}

	public function __construct()
	{

	}
}

