<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Diagnostics\LoggerStorages;

/**
 * Http logger storage
 *
 * @author	Patrik Votoček
 */
class Http extends \Nette\Object implements \Nella\Diagnostics\ILoggerStorage
{
	/** @var string */
	private $url;
	/** @var string */
	private $appId;
	/** @var string */
	private $appSecret;

	/**
	 * @param string
	 * @param string
	 * @param string
	 */
	public function __construct($appId, $appSecret, $url)
	{
		if (function_exists('ini_get') && !ini_get('allow_url_fopen')) {
			throw new \Nette\InvalidStateException('allow_url_fopen is not allowed');
		}

		$this->url = $url;
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}

	/**
	 * @param array
	 */
	public function save(array $data)
	{
		$headers = array(
			'appId' => "X-LoggerAuth-AppId: {$this->appId}",
			'appSecret' => "X-LoggerAuth-AppSecret: {$this->appSecret}",
		);
		$req = @stream_context_create(array(
			'http' => array(
				'header' => "Content-Type: application/x-www-form-urlencoded\r\n".implode("\r\n", $headers),
				'method' => 'POST',
				'content' => http_build_query($data),
			)
		));
		$fp = @fopen($this->url, 'r', FALSE, $req);
		@fclose($fp);
	}
}

