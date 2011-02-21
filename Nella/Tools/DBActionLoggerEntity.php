<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Tools;

/**
 * Database action logger entity
 *
 * @entity(repositoryClass="Nella\Models\Repository")
 * @table(name="actionlogs")
 * 
 * @author	Patrik Votoček
 */
class DBActionLoggerEntity extends \Nella\Models\Entity
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
     * @manyToOne(targetEntity="Nella\Security\IdentityEntity")
     * @joinColumn(name="user_id", referencedColumnName="id")
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
	 * @return DBActionLoggerEntity
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
	 * @return DBActionLoggerEntity
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
	 * @return DBActionLoggerEntity
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
	 * @return DBActionLoggerEntity
	 */
	public function setUser(\Nella\Security\IdentityEntity $user = NULL)
	{
		$this->user = $user;
		return $this;
	}
	
	/**
	 * @prePersist
	 * @preUpdate
	 * 
	 * @throws \Nella\Models\EmptyValuesException
	 */
	public function check()
	{
		parent::check();
		
		if ($this->module === NULL) {
			throw new \Nella\Models\EmptyValuesException('module', "Module value must be non empty string");
		}
		if ($this->action === NULL) {
			throw new \Nella\Models\EmptyValuesException('action', "Action value must be non empty string");
		}
	}
}
