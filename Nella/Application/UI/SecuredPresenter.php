<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Application\UI;

/**
 * Base secured presenter
 *
 * @author	Patrik Votoček
 */
abstract class SecuredPresenter extends Presenter
{
	/** @var string */
	protected $loginLink = ':Security:Frontend:login';

	/**
	 * Checks for requirements such as authorization
	 *
	 * @param \Nette\Reflection\ClassType
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function checkRequirements($element)
	{
		parent::checkRequirements($element);
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect($this->loginLink, array('backlink' => $this->storeRequest()));
		}
	}

	public function handleLogout()
	{
		$this->getUser()->logout(TRUE);
		$this->redirect($this->loginLink);
	}
}

