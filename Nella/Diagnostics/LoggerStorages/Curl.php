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
 * Curl http logger storage
 *
 * @author	Patrik Votoček
 */
class Curl extends \Nette\Object implements \Nella\Diagnostics\ILoggerStorage
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
		if (!extension_loaded('curl')) {
			throw new \Nette\InvalidStateException('Missing cURL extension');
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

