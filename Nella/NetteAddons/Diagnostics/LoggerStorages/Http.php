<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\NetteAddons\Diagnostics\LoggerStorages;

/**
 * Http logger storage
 *
 * @author	Patrik Votoček
 */
class Http extends \Nette\Object implements \Nella\NetteAddons\Diagnostics\ILoggerStorage
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
		if (!extension_loaded('curl') && (function_exists('ini_get') && !ini_get('allow_url_fopen'))) {
			throw new \	InvalidStateException('Missing cURL extension or allow_url_fopen ON');
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
		if (function_exists('ini_get') && ini_get('allow_url_fopen')) {
			$req = @stream_context_create(array(
				'http' => array(
					'header' => "Content-Type: application/x-www-form-urlencoded\r\n".implode("\r\n", $headers),
					'method' => 'POST',
					'content' => http_build_query($data),
				)
			));
			$fp = @fopen($this->url, 'r', FALSE, $req);
			@fclose($fp);
		} else {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_NOBODY, TRUE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);

			curl_exec($ch);
		}
	}
}
