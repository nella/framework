<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Tools;

/**
 * File logger
 *
 * @author	Patrik Votoček
 */
class FileLogger extends \Nette\Object implements ILogger
{
	/** @var string */
	public static $defaultLogFile = "messages.log";
	/** @var string */
	private $file;

	/**
	 * @param string
	 */
	public function __construct($file = NULL)
	{
		$logDir = \Nette\Diagnostics\Debugger::$logDirectory;
		if ($file) {
			$this->file = $logDir . "/" . $file;
		} else {
			$this->file = $logDir . "/" . static::$defaultLogFile;
		}
	}

	/**
	 * @param string
	 * @param int	message priority level
	 * @throws \Nette\IOException
	 */
	public function logMessage($message, $level = self::ERROR)
	{
		$levels = array(
			static::INFO => "info",
			static::ERROR => "error",
			static::WARNING => "warning",
			static::FATAL => "fatal",
			static::DEBUG => "debug"
		);

		if (!@file_put_contents($this->file, "[" . date("Y-m-d H:i:s P") . "] {$levels[$level]}: $message" . PHP_EOL, FILE_APPEND)) {
			throw new \Nette\IOException("File '{$this->file}' is not writable");
		}
	}
}