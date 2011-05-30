<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Utils\LoggerStorages;

use Nette\Security\IIdentity;

/**
 * File action logger
 *
 * @author	Patrik Votoček
 */
class FileStorage extends \Nette\Object implements \Nella\Utils\IActionLogger
{
	/** @var string */
	public static $defaultLogFile = "actions.log";
	/** @var string */
	private $file;
	/** @var \Nette\Security\IIdentity */
	private $user;

	/**
	 * @param \Nette\Security\IIdentity
	 * @param string
	 */
	public function __construct(IIdentity $identity, $file = NULL)
	{
		$this->user = $identity;
		$logDir = \Nette\Diagnostics\Debugger::$logDirectory;
		if ($file) {
			$this->file = $logDir . "/" . $file;
		} else {
			$this->file = $logDir . "/" . static::$defaultLogFile;
		}
	}

	/**
	 * @param string
	 * @param string
	 * @param string
	 * @param \Nette\Security\IIdentity
	 * @throws \Nette\IOException
	 */
	public function logAction($module, $action = self::OTHER, $message = "", \Nette\Security\IIdentity $user = NULL)
	{
		if (!$user) {
			$user = $this->user;
		}

		$data = "[" . date("Y-m-d H:i:s P") . "] $module:$action: $message #{$user->getId()}";
		if (!@file_put_contents($this->file, $data . PHP_EOL, FILE_APPEND)) {
			throw new \Nette\IOException("File '{$this->file}' is not writable");
		}
	}
}
