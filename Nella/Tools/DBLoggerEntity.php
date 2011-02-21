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
 * Database logger entity
 * 
 * @entity(repositoryClass="Nella\Models\Repository")
 * @table(name="logs")
 * 
 * @author	Patrik Votoček
 * @hasLifecycleCallbacks
 * 
 * @property string $message
 * @property int $level
 */
class DBLoggerEntity extends \Nella\Models\Entity
{
	/**
	 * @column(length=256)
	 * @var string
	 */
	private $message;
	/**
	 * @column(type="integer")
	 * @var int
	 */
	private $level;
	
	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * @param string
	 * @return DBLoggerEntity
	 */
	public function setMessage($message)
	{
		$message = trim($message);
		$this->message = $message === "" ? NULL : $message;
		return $this;
	}
	
	/**
	 * @param bool
	 * @return int
	 */
	public function getLevel($asString = FALSE)
	{
		if (!$asString) {
			return $this->level;
		} else {
			$levels = array(
				static::INFO => "info", 
				static::ERROR => "error", 
				static::WARNING => "warning", 
				static::FATAL => "fatal", 
				static::DEBUG => "debug"
			);
			
			return isset($levels[$this->level]) ? $levels[$this->level] : NULL;
		}
	}
	
	/**
	 * @param int
	 * @return DBLoggerEntity
	 */
	public function setLevel($level)
	{
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
		
		if ($this->message === NULL) {
			throw new \Nella\Models\EmptyValuesException('message', "Message value must be non empty string");
		}
		if ($this->level === NULL) {
			throw new \Nella\Models\EmptyValuesException('level', "Level value must be non empty int");
		}
	}
}
