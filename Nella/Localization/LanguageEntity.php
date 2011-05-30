<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Localization;

/**
 * Language entity
 *
 * @entity(repositoryClass="Nella\Models\Repository")
 * @table(name="langs")
 *
 * @author	Patrik Votoček
 *
 * @property string $name
 * @property string $nativeName
 * @property string $short
 */
class LanguageEntity extends \Nella\Models\Entity
{
	/**
	 * @column
	 * @var string
	 */
	private $name;
	/**
	 * @column
	 * @var string
	 */
	private $nativeName;
	/**
	 * @column(length=5)
	 * @var string
	 */
	private $short;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string
	 * @return LanguageEntity
	 */
	public function setName($name)
	{
		$name = trim($name);
		$this->name = $name === "" ? NULL : $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNativeName()
	{
		return $this->nativeName;
	}

	/**
	 * @param string
	 * @return LanguageEntity
	 */
	public function setNativeName($nativeName)
	{
		$nativeName = trim($nativeName);
		$this->nativeName = $nativeName === "" ? NULL : $nativeName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getShort()
	{
		return $this->short;
	}

	/**
	 * @param string
	 * @return LanguageEntity
	 */
	public function setShort($short)
	{
		$short = trim($short);
		$this->short = $short === "" ? NULL : $short;
		return $this;
	}
}
