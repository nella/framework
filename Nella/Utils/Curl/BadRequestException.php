<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Utils\Curl;

class BadRequestException extends \Nette\InvalidStateException
{
	/** @var Response */
	private $response;

	/**
	 * @param string
	 * @param mixed
	 * @param Response
	 */
	public function __costruct($message, $code, Response $response)
	{
		parent::__construct($message, $code);
		$this->response = $response;
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}
}