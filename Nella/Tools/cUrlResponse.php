<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Tools;

use Nette\StringUtils;
 
/**
 * cUrl wrapper response object
 *
 * @author	Patrik Votoček
 * 
 * @property-read array $headers
 * @property-read string $body
 * @property-read Nella\Tools\cUrlRequest $request
 */
class cUrlResponse extends \Nette\Object
{
	/** regexp's for parsing */
	const HEADER_REGEXP = '~(?P<header>.*?)\:\s(?P<value>.*)~', 
		VERSION_AND_STATUS = '~HTTP/(?P<version>\d\.\d)\s(?P<code>\d\d\d)\s(?P<status>.*)~', 
		CONTENT_TYPE = '~^(?P<type>[^;]+);[\t ]*charset=(?P<charset>.+)$~i';
	
	/** @var Nella\Tools\cUrlRequest */
	private $request;
	/** @var array */
	private $headers = array();
	/** @var string */
	private $body = NULL;
	
	/**
	 * @param string
	 * @author	Filip Procházka
	 */
	private function parseHeaders($headers)
	{
		// Extract the version and status from the first header
		$version_and_status = array_shift($headers);
		$matches = StringUtils::match($version_and_status, self::VERSION_AND_STATUS);
		if (count($matches) > 0) {
			$this->headers['Http-Version'] = $matches['version'];
			$this->headers['Status-Code'] = $matches['code'];
			$this->headers['Status'] = $matches['code'].' '.$matches['status'];
		}

		// Convert headers into an associative array
		foreach ($headers as $header) {
			$matches = StringUtils::match($header, self::HEADER_REGEXP);
			$this->headers[$matches['header']] = $matches['value'];
		}
	}
	
	/**
	 * @param string
	 * @param cUrlRequest
	 */
	public function __construct($response, cUrlRequest $request)
	{
		$this->request = $request;
		
		$headers = StringUtils::split(substr($response, 0, $this->request->info['header_size']), "~[\n\r]+~", PREG_SPLIT_NO_EMPTY);
		$this->parseHeaders($headers);
		$this->body = substr($response, $this->request->info['header_size']);
	}
	
	/**
	 * @return cUrlRequest
	 */
	public function getRequest()
	{
		return $this->request;
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
	 * @return mixed
	 */
	public function getHeader($key)
	{
		return isset($this->headers[$key]) ? $this->headers[$key] : NULL;
	}
	
	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getBody();
	}
}
