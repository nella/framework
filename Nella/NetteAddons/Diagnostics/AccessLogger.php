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
		$f = new \Nette\Http\RequestFactory;
		$req = $f->setEncoding('UTF-8')->createHttpRequest();
		$data = array(
			'datetime' => date('c'),
			'ua' => $req->getHeader('user-agent', NULL),
			'ip' => $req->getRemoteAddress(),
			'host' => $req->getRemoteHost(),
			'method' => $req->getMethod(),
			'url' => (string)$req->getUrl(),
			'code' => $res->getCode(),
			'referer' => $req->getReferer(),
			'time' => isset($_SERVER['REQUEST_TIME_FLOAT']) ? (microtime(TRUE)-$_SERVER['REQUEST_TIME_FLOAT'])*1000 : 0,
			'memory' => function_exists('memory_get_peak_usage') ? number_format(memory_get_peak_usage() / 1000000, 2, '.', ' ') : 0,
		);

		$this->storage->save($data);
	}
}