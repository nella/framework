<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Security;

use Nella\Forms\Form,
	Nette\Security\IAuthenticator;

/**
 * Backend security presenter
 *
 * @author	Patrik Votoček
 */
class BackendPresenter extends \Nella\Application\UI\BackendPresenter
{
	/** @var array */
	protected $users;

	public function actionDefault()
	{
		$service = $this->getDoctrineContainer()->getService('Nella\Security\CredentialsEntity');
		$this->users = $service->repository->findAll();
	}

	/**
	 * @allowed(resource="security",privilege="delete")
	 * @param int
	 */
	public function handleDelete($id)
	{
		$service = $this->getDoctrineContainer()->getService('Nella\Security\CredentialsEntity');
		$credentials = $service->repository->findOneByIdentity($id);

		if (!$credentials) {
			$this->flashMessage("User with id $id does not exist", 'succes');
			$this->redirect(':Security:Backend:');
		}

		$service->delete($credentials);
		$this->logAction("Security", \Nella\Utils\IActionLogger::DELETE, "Removed user {$credentials->username}");
		$this->flashMessage("User '{$credentials->username}' successfuly removed", 'success');
		$this->redirect(':Security:Backend:');
	}

	public function renderDefault()
	{
		$this->template->users = $this->users;
	}

	public function actionProfile()
	{
		$service = $this->getDoctrineContainer()->getService('Nella\Security\CredentialsEntity');
		$credentials = $service->repository->findOneByIdentity($this->getUser()->identity->id);

		$this['emailForm']->setDefaults($credentials);
	}

	/**
	 * @param string
	 */
	protected function createComponentEmailForm($name)
	{
		new Forms\Email($this, $name);
	}

	/**
	 * @param string
	 */
	protected function createComponentPasswordForm($name)
	{
		new Forms\Password($this, $name);
	}

	/**
	 * @allowed(resource="security",privilege="create")
	 * @param string
	 */
	protected function createComponentAddUserForm($name)
	{
		new Forms\AddUser($this, $name);
	}
}