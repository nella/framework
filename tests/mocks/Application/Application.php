<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Mocks\Application;

class Application extends \Nette\Application\Application
{
	public function __construct()
	{
		$presenterFactory = new \Nette\Application\PresenterFactory(__DIR__, new \Nette\DI\Container);
		$router = new \Nette\Application\Routers\Route('');
		$httpRequest = new \Nette\Http\Request(new \Nette\Http\UrlScript);
		$httpResponse = new \Nette\Http\Response;
		parent::__construct($presenterFactory, $router, $httpRequest, $httpResponse);
	}
}