<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Security\Forms;
use Nette\Security\IAuthenticator;

/**
 * Credentials login form
 *
 * @author	Patrik Votoček
 */
class Login extends \Nella\Forms\Form
{
	protected $dashboardLink = ":Dashboard:Backend:default";

	protected function setup()
	{
		parent::setup();

		$this->addText('username', "Username")->setRequired();
		$this->addPassword('password', "Password")->setRequired();

		$this->addCheckbox('remember', "Remember me")->setDisabled(); // @todo

		$this->addSubmit('sub', "Login");

		$this->onSuccess[] = callback($this, "process");
	}

	public function process()
	{
		$values = $this->getValues();
		$presenter = $this->getPresenter();

		try {
			$presenter->getUser()->login($values['username'], $values['password']);
			$backlink = $presenter->getParam('backlink');
			if ($backlink) {
				$presenter->redirect($presenter->getApplication()->restoreRequest($backlink));
			} else {
				$presenter->redirect($this->dashboardLink);
			}
		} catch (\Nette\Security\AuthenticationException $e) {
			if ($e->getCode() == IAuthenticator::INVALID_CREDENTIAL) {
				$this['password']->addError("Password is not valid");
			} elseif ($e->getCode() == IAuthenticator::IDENTITY_NOT_FOUND) {
				$this['username']->addError("Username does not exist");
			} else {
				$presenter->addError("Username or password is invalid");
			}
		}
	}
}
