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
 * Token entity
 *
 * @entity
 * @table(name="tokens")
 *
 * @author	Patrik Votoček
 *
 * @property string $key
 * @property string $secret
 * @property string $type
 */
class TokenEntity extends \Nella\Doctrine\Entity
{
	/**
	 * @column(length=32,unique=true,name="`key`")
	 * @var string
	 */
	private $key;
	/**
	 * @column(length=40)
	 * @var string
	 */
	private $secret;
	/**
	 * @column
	 * @var string
	 */
	private $type;

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param string
	 * @return TokenEntity
	 */
	public function setKey($key)
	{
		$this->key = $this->sanitizeString($key);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * @param string
	 * @return TokenEntity
	 */
	public function setSecret($secret)
	{
		$this->secret = $this->sanitizeString($secret);
		return $this;
	}

	/**
	 * @param string
	 * @return bool
	 */
	public function validateSecret($secret)
	{
		return $this->secret == $secret;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string
	 * @return TokenEntity
	 */
	public function setType($type)
	{
		$this->type = $this->sanitizeString($type);
		return $this;
	}
}
