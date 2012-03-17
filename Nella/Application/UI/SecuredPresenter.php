<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
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
	protected $loginLink = ":Security:Frontend:login";

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
			$this->redirect($this->loginLink, array('backlink' => $this->getApplication()->storeRequest()));
		}
	}

	public function handleLogout()
	{
		$this->getUser()->logout(TRUE);
		$this->redirect($this->loginLink);
	}
}
