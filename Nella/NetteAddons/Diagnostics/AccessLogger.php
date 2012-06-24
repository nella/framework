<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Diagnostics;

/**
 * Remote access logger
 *
 * @author	Patrik Votoček
 */
class AccessLogger extends \Nette\Object
{
	/** @var string */
	private $storage;

	/**
	 * @param ILoggerStorage
	 */
	public function __construct(ILoggerStorage $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * @param \Nette\Http\Response
	 */
	public function log(\Nette\Http\Response $res)
	{
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL;
		if (isset($_SERVER['HTTP_X_REAL_IP'])) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		$f = new \Nette\Http\RequestFactory;
		$req = $f->setEncoding('UTF-8')->createHttpRequest();
		$data = array(
			'datetime' => date('c'),
			'ua' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL,
			'ip' => $ip,
			'host' => isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : NULL,
			'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : NULL,
			'url' => (isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off') ? 'https://' : 'http://')
					. (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : ''))
					. $_SERVER['REQUEST_URI'],
			'code' => function_exists('http_response_code') ? http_response_code() : $res->getCode(),
			'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL,
			'time' => isset($_SERVER['REQUEST_TIME_FLOAT']) ? (microtime(TRUE)-$_SERVER['REQUEST_TIME_FLOAT'])*1000 : 0,
			'memory' => function_exists('memory_get_peak_usage') ? number_format(memory_get_peak_usage() / 1000000, 2, '.', ' ') : 0,
		);

		$this->storage->save($data);
	}
}