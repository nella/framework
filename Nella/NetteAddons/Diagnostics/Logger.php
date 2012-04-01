<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Diagnostics;

use Nette\Utils\Strings,
	Nette\Diagnostics\Debugger;

/**
 * Diagnostics logger
 *
 * @author	Patrik Votoček
 */
class Logger extends \Nette\Diagnostics\Logger
{
	/** @var ILoggerStorage */
	private $storage;

	/**
	 * @param ILoggerStorage
	 */
	public function __construct(ILoggerStorage $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * @param string|array
	 * @param string
	 */
	public function log($message, $priority = self::INFO)
	{
		$data = array('priority' => $priority, 'ip' => $_SERVER['SERVER_ADDR']);

		if (is_array($message)) {
			$data['datetime'] = $this->datetimeToIso($message[0]);
			if (isset($message[1])) {
				$data['message'] = $message[1];
			}
			if (isset($message[2])) {
				$data['url'] = substr($message[2], 4);
			}
			if (isset($message[3])) {
				$file = substr($message[3], 5);
				$data['path'] = Debugger::$logDirectory . '/' . $file;
			}
			$data['line'] = implode(' ', $message);
		} else {
			$data['line'] = $message;
		}

		$res = parent::log($data['line'], $priority);

		$this->storage->save($data);

		return $res;
	}

	/**
	 * @param string
	 * @return string
	 */
	private function datetimeToIso($date)
	{
		$date = \DateTime::createFromFormat('[Y-m-d H:i:s]', $date);
		if ($date instanceof \DateTime) {
			return $date->format('c');
		}
		return date('c');
	}

	/**
	 * @param string
	 * @param string
	 * @param string
	 */
	public static function register($appId, $key, $url = 'http://localhost:50921/api/log/index.json')
	{
		$qs = "__logger=get&id=$appId&key=$key&file=";
		if (isset($_SERVER['QUERY_STRING']) && Strings::startsWith($_SERVER['QUERY_STRING'], $qs)) {
			$path = Strings::substring($_SERVER['QUERY_STRING'], Strings::length($qs));
			if (strpos($path, '/') || strpos($path, '\\')) {
				exit;
			}
			$path = Debugger::$logDirectory . '/' . $path;
			if (!file_exists($path)) {
				die('error');
			}
			echo file_get_contents($path);
			exit;
		}

		$storage = new LoggerStorages\Http($appId, $key, $url);
		$logger = new static($storage);
		$logger->directory = & Debugger::$logger->directory;
		$logger->email = & Debugger::$logger->email;
		$logger->mailer = & Debugger::$logger->mailer;
		static::$emailSnooze = & Logger::$emailSnooze;

		Debugger::$logger = $logger;
	}
}