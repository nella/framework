<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Security\Forms;

/**
 * Add user form
 *
 * @author	Patrik Votoček
 */
class AddUser extends \Nella\Forms\Form
{
	public $successLink = ":Security:Backend:";

	protected function setup()
	{
		parent::setup();

		$this->addText('username', "Username")->setRequired();
		$this->addEmail('email', "E-mail")->setRequired();
		$this->addPassword('password', "Password");
		$this->addPassword('password2', "Re-Password")->addCondition(static::FILLED)
			->addRule(static::EQUAL, NULL, $this['password']);
		$this->addSelect('role', "Role", array(1 => "Admin")); // @todo
		$this->addSelect('lang', "Lang", array('en' => "English"))->setRequired(); // @todo

		$this->addSubmit('sub', "Add");

		$this->onSubmit[] = callback($this, 'process');
	}

	public function process()
	{
		$values = $this->getValues();
		$presenter = $this->getPresenter();
		$service = $this->getDoctrineContainer()->getService('Nella\Security\CredentialsEntity');

		try {
			$entity = $service->create($values);
			$presenter->logAction("Security", \Nella\Utils\IActionLogger::CREATE, "Created user '{$entity->username}'");
			$presenter->flashMessage("User '{$entity->username}' successfuly added", 'success');
			$presenter->redirect($this->successLink);
		} catch (\Nella\Models\InvalidEntityException $e) {
			$this->processException($e);
		} catch (\Nella\Models\DuplicateEntryException $e) {
			$this['username']->addError("Username %value already exist");
		}
	}
}
