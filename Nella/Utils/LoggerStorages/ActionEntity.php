<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Utils\LoggerStorages;

/**
 * Database action logger entity
 *
 * @entity
 * @table(name="logger")
 * @service(class="Nella\Utils\LoggerStorages\DatabaseStorage")
 *
 * @author	Patrik Votoček
 */
class ActionEntity extends \Nella\Doctrine\Entity
{
	/**
	 * @column(length=128)
	 * @var string
	 */
	private $module;
	/**
	 * @column(length=32)
	 * @var string
	 */
	private $action;
	/**
	 * @column(length=256, nullable=true)
	 * @var string
	 */
	private $message;
	/**
     * @manyToOne(targetEntity="Nella\Security\IdentityEntity", fetch="EAGER")
     * @joinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
	 * @var \Nella\Security\IdentityEntity
	 */
	private $user;

	/**
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * @param string
	 * @return ActionEntity
	 */
	public function setModule($module)
	{
		$module = trim($module);
		$this->module = $module === "" ? NULL : $module;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param string
	 * @return ActionEntity
	 */
	public function setAction($action)
	{
		$action = trim($action);
		$this->action = $action === "" ? NULL : $action;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param string
	 * @return ActionEntity
	 */
	public function setMessage($message)
	{
		$action = trim($message);
		$this->message = $message === "" ? NULL : $message;
		return $this;
	}

	/**
	 * @return \Nella\Security\IdentityEntity
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param \Nella\Security\IdentityEntity
	 * @return ActionEntity
	 */
	public function setUser(\Nella\Security\IdentityEntity $user = NULL)
	{
		$this->user = $user;
		return $this;
	}
}
