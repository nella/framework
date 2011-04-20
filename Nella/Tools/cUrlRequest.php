<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Tools;

use Nette\Http\Url,
	Nette\Strings;

/**
 * Nella cUrl wrapper request class
 *
 * @author	Patrik Votoček
 *
 * @property-read string $url
 * @property-read array $options
 * @property-read array $headers
 * @property-read array $proxies
 * @property string $userAgent
 */
class cUrlRequest extends \Nette\Object
{
	/** Available HTTP methods of requests */
	const GET = 'GET',
		POST = 'POST',
		PUT = 'PUT',
		DELETE = 'DELETE',
		HEAD = 'HEAD';
		//DOWNLOAD = 'DOWNLOAD'; // Not implemented yet

	/**
	 * @var array
	 */
	private $options = array();
	/**
	 * @var array
	 */
	private $headers = array();
	/**
	 * @var array
	 */
	private $proxies = array();
	/**
	 * @var resource
	 */
	private $resource;
	/**
	 * @var string
	 */
	private $url;
	/**
	 * @var cUrlResponse
	 */
	private $response;

	/**
	 * @param string
	 * @param array
	 * @param array
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\InvalidStateException
	 */
	public function __construct($url = NULL, $options = array(), $headers = array())
	{
		if (!extension_loaded('curl')) {
			throw new \Nette\InvalidStateException("Curl extension is not loaded!");
		}

		$ua = 'Nella\Tools\cUrl ' . \Nella\Framework::VERSION . " (http://nella-project.org)";
		$this->setOption('useragent', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : $ua);
		$this->setOption('returntransfer', TRUE);

		$this->url = $url;
		foreach ($options as $key => $value) {
			$this->setOption($key, $value);
		}
		foreach ($headers as $key => $value) {
			$this->setHeader($key, $value);
		}
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param string
	 * @return string|NULL
	 * @throws \Nette\InvalidArgumentException
	 */
	public function getOption($key)
	{
		$key = strtoupper($key);
		if (!defined('CURLOPT_' . str_replace('CURLOPT_', '', $key))) {
			throw new \Nette\InvalidArgumentException("cUrl option '$key' does not exist");
		}

		return isset($this->options[$key]) ? $this->options[$key] : NULL;
	}

	/**
	 * @param string
	 * @param string|NULL
	 * @return cUrlRequest
	 * @throws \Nette\InvalidArgumentException
	 */
	public function setOption($key, $value)
	{
		$key = strtoupper($key);
		if (!defined('CURLOPT_' . str_replace('CURLOPT_', '', $key))) {
			throw new \Nette\InvalidArgumentException("cUrl option '$key' does not exist");
		}

		$value = trim($value);
		$this->options[$key] = $value == "" ? NULL : $value;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * @param string
	 * @return string|NULL
	 */
	public function getHeader($key)
	{
		$key = strtoupper($key);
		return isset($this->headers[$key]) ? $this->headers[$key] : NULL;
	}

	/**
	 * @param string
	 * @param string|NULL
	 * @return cUrlRequest
	 */
	public function setHeader($key, $value)
	{
		$key = strtoupper($key);
		$value = trim($value);
		$this->headers[$key] = $value == "" ? NULL : $value;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getProxies()
	{
		return $this->proxies;
	}

	/**
	 * @param string
	 * @param int
	 * @param string|NULL
	 * @param string|NULL
	 * @param int time in seconds
	 * @return cUrlRequest
	 */
	public function addProxy($ip, $port = 3128, $username = NULL, $password = NULL, $timeout = 15)
	{
		$this->proxies[] = (object) array(
			'ip' => $ip,
			'port' => $port,
			'username' => $username,
			'password' => $password,
			'timeout' => $timeout,
		);

		return $this;
	}

	/**
	 * @return array
	 */
	public function getInfo()
	{
		if (gettype($this->resource) == 'resource' && get_resource_type($this->resource) == 'curl') {
			return curl_getinfo($this->resource);
		} else {
			return array();
		}
	}

	/**
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->getOption('useragent');
	}

	/**
	 * @param string
	 * @return cUrlRequest
	 * @throws \Nette\InvalidArgumentException
	 */
	public function setUserAgent($userAgent)
	{
		$userAgent = trim($userAgent);
		$this->setOption('useragent', $userAgent == "" ? NULL : $userAgent);
		if (!$this->getOption('useragent')) {
			throw new \Nette\InvalidArgumentException("User agent string must be a non-empty string");
		}
		return $this;
	}

	/**
	 * Formats and adds custom headers to the current request
	 *
	 * @author	Filip Procházka
	 */
	protected function setupHeaders()
	{
		$headers = array();
		foreach ($this->headers as $key => $value) {
			//fix HTTP_ACCEPT_CHARSET to Accept-Charset
			$key = Strings::replace($key, array(
				'~^HTTP_~i' => '',
				'~_~' => '-'
			));
			$key = Strings::replace($key, array(
				'~(?P<word>[a-z]+)~i',
			), function($match) {
				return ucfirst(strtolower(current($match)));
			});

			if ($key == 'Et') {
				$key = 'ET';
			}

			$headers[] = (!is_int($key) ? ($key . ': ') : '') . $value;
		}

		if (count($this->headers) > 0) {
			curl_setopt($this->resource, CURLOPT_HTTPHEADER, $headers);
		}
	}

	/**
	 * Sets the CURLOPT options for the current request
	 *
	 * @author	Filip Procházka
	 * @param array
	 */
	protected function setupOptions($post = array())
	{
		$this->setOption('url', $this->url);

		if ($post && is_array($post)) {
			$post = http_build_query($post, '', '&');
			$this->setOption('postfields', $post);
		}

		// Prepend headers in response
		$this->setOption('header', TRUE); // this makes me literally cry sometimes

		// we shouldn't trust to all certificates but we have to!
		if ($this->getOption('ssl_verifypeer') === NULL) {
			$this->setOption('ssl_verifypeer', FALSE);
		}

		// Set all cURL options
		foreach ($this->options as $key => $value) {
			curl_setopt($this->resource, constant('CURLOPT_' . $key), $value);
		}
	}

	/**
	 * Setup the associated Curl options for a request method
	 *
	 * @param string
	 */
	protected function setupMethod($method)
	{
		$method = strtoupper($method);

		switch ($method) {
			case self::HEAD:
				$this->setOption('nobody', TRUE);
				break;
			case self::GET:
			//case self::DOWNLOAD:
				$this->setOption('httpget', TRUE);
				break;
			case self::POST:
				$this->setOption('post', TRUE);
				break;
			default:
				$this->setOption('customrequest', $method);
				break;
		}
	}

	/**
	 * @param int
	 */
	protected function tryProxy($i)
	{
		if (count($this->proxies) > $i) {
			$proxy = $this->proxies[$i];
			$this->setOption('proxy', $proxy->ip . ':' . $proxy->port);
			$this->setOption('proxyport', $proxy->port);
			$this->setOption('timeout', $proxy->timeout);

			if ($proxy->username !== NULL && $proxy->password !== NULL) {
				$this->setOption('proxyuserpwd', $proxy->username . ':' . $proxy->password);
			}
		} else {
			unset($this->options['PROXY'], $this->options['PROXYPORT'], $this->options['PROXYTYPE'], $this->options['PROXYUSERPWD']);
		}
	}

	private function close()
	{
		if (gettype($this->resource) == 'resource' && get_resource_type($this->resource) == 'curl') {
			@curl_close($this->resource);
		}

		$this->resource = NULL;
	}

	public function __destruct()
	{
		$this->close();
	}

	/**
	 * @return void
	 * @throws \Nette\InvalidStateException
	 */
	private function open()
	{
		$this->close();

		return $this->resource = curl_init($this->url);
	}

	/**
	 * @return void
	 * @throws \Nette\InvalidStateException
	 */
	private function execute()
	{
		$response = curl_exec($this->resource);
		$error = curl_error($this->resource) . " (#" . curl_errno($this->resource) . ")";
		$info = curl_getinfo($this->resource);

		if ($response) {
			$this->response = new cUrlResponse($response, $this);
		} else {
			throw new \Nette\InvalidStateException($error, $info['http_code']);
		}
	}

	/**
	 * @param string
	 * @param string
	 * @param array
	 * @param int
	 * @return cUrlResponse
	 * @throws \Nette\InvalidStateException
	 * @throws cUrlBadRequestException
	 */
	protected function run($method = self::GET, $url = NULL, $post = array(), $cycles = 1)
	{
		if ($cycles > 5) {
			throw new \Nette\InvalidStateException("Redirect loop");
		}

		if (!is_string($url) && $url !== '') {
			if (!$this->url) {
				throw new \Nette\InvalidStateException("cUrl invalid URL '$url'");
			}
		} else {
			$this->url = $url;
		}

		$i = 0;
		do {
			$this->open();

			$this->tryProxy($i++);
			$this->setupMethod($method);
			$this->setupOptions($post);
			$this->setupHeaders();

			$this->execute();
		} while (curl_errno($this->resource) == 6 && count($this->proxies) < $i);
		$this->close();

		$fixUrl = function ($from, $to) {
			$from = new Url($from);
			$to = new Url($to);

			if (empty($to->scheme)) { // scheme
				if (empty($from->scheme)) {
					throw new \Nette\InvalidStateException("Missign URL scheme!");
				}

				$to->scheme = $from->scheme;
			}

			if (empty($to->host)) { // host
				if (empty($from->host)) {
					throw new \Nette\InvalidStateException("Missign URL host!");
				}

				$to->host = $from->host;
			}

			if (empty($to->path)) { // path
				$to->path = $from->path;
			}

			return $to->absoluteUri;
		};

		$headers = $this->response->headers;
		if ($headers['Status-Code'] < 400) {
			if (isset($headers['Location']))  {
				$url = $fixUrl($url, $headers['Location']);
				$this->run($method, (string) $url, $post, ++$cycles);
			}
		} else {
			throw new cUrlBadRequestException($headers['Status'], $this->response->info['http_code'], $this->response);
		}
	}

	/**
	 * @param string
	 * @param string
	 * @param array
	 * @return cUrlResponse
	 */
	public function getResponse($method = self::GET, $url = NULL, $post = array())
	{
		if (!($this->response instanceof cUrlResponse)) {
			$this->run($method, $url, $post);
		}

		return $this->response;
	}

	/**
	 * @return cUrlRequest
	 */
	public function __clone()
	{
		$this->response = NULL;
		return $this;
	}
}

class cUrlBadRequestException extends \Nette\InvalidStateException
{
	/** @var cUrlResponse */
	private $response;

	/**
	 * @param string
	 * @param mixed
	 * @param cUrlResponse
	 */
	public function __costruct($message, $code, cUrlResponse $response)
	{
		parent::__construct($message, $code);
		$this->response = $response;
	}

	/**
	 * @return cUrlResponse
	 */
	public function getResponse()
	{
		return $this->response;
	}
}
