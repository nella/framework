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
		$data = array('type' => $priority, 'ip' => $_SERVER['SERVER_ADDR']);

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
	 * @param string|bool password for log file downloader
	 * @param string
	 */
	public static function register($appId, $appSecret, $password = FALSE, $url = 'http://localhost:50921/api/log.json')
	{
		if (isset($_GET['__getfile'])) {
			@header('X-Frame-Options: ');
			$data = json_decode(base64_decode($_GET['__getfile']), TRUE);
			if ($password === FALSE) {
				die('No password set');
			} elseif (!array_key_exists('password', $data) || !array_key_exists('path', $data)) {
				die('Missing data');
			} elseif ($data['appid'] != $appId || $data['appsecret'] != $appSecret || $data['password'] != $password) {
				die('Invalid credentials');
			} elseif (!file_exists($data['path'])) {
				die('Invalid file');
			} elseif (strncmp(realpath($data['path']), realpath(Debugger::$logger->directory), strlen(realpath(Debugger::$logger->directory))) !== 0) {
				die('Path is not valid log dir');
			}

			echo file_get_contents($data['path']);

			exit;
		}

		$storage = new LoggerStorages\Http($appId, $appSecret, $url);
		$logger = new static($storage);
		$logger->directory = & Debugger::$logger->directory;
		$logger->email = & Debugger::$logger->email;
		$logger->mailer = & Debugger::$logger->mailer;
		static::$emailSnooze = & Logger::$emailSnooze;

		Debugger::$logger = $logger;
	}
}