<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application;

/**
 * Base backend presenter
 *
 * @author	Patrik Votoček
 */
abstract class BackendPresenter extends Presenter
{
	/** @var string */
	protected $loginLink = ":Security:Frontend:login";
	
	protected function startup()
	{
		parent::startup();
		
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->getUser()->logoutReason === \Nette\Web\User::INACTIVITY) {
				$this->flashMessage(__("You have been logged out due to inactivity. Please login again."), \Nella\FLASH_INFO);
			}
				
			$this->redirect($this->loginLink, array('backlink' => $this->getApplication()->storeRequest()));
		}
			
		try {
			$this->getUser()->getIdentity();
			$this->lang = $this->getUser()->identity->entity->lang;
		} catch (\InvalidStateException $e) {
			if ($this->getUser()->logoutReason === \Nette\Web\User::INACTIVITY) {
				$this->flashMessage(__("Your login session expired. Please login again."), \Nella\FLASH_ERROR);
			}

			$this->getUser()->logout(TRUE);
			$this->redirect($this->loginLink, array('backlink' => $this->getApplication()->storeRequest()));
		}
		
		$ref = new \Nette\Reflection\ClassReflection(get_called_class());
		$method = $this->formatActionMethod($this->getAction());
		if ($ref->hasMethod($method) && !$this->isAllowed($method)) {
			throw new \Nette\Application\BadRequestException("You don't have permission for this '{$this->getAction()}' action", 403);
		}
		$method = $this->formatRenderMethod($this->getView());
		if ($ref->hasMethod($method) && !$this->isAllowed($method)) {
			throw new \Nette\Application\BadRequestException("You don't have permission for this '{$this->getView()}' view", 403);
		}
		$method = $this->formatSignalMethod($this->getSignal());
		if ($ref->hasMethod($method) && !$this->isAllowed($method)) {
			throw new \Nette\Application\BadRequestException("You don't have permission for this '{$this->getSignal()}' signal", 403);
		}

		$this->setLayout('backend');
	}
	
	/**
	 * Is method allowed for loggened user
	 *
	 * @param string
	 * @return bool
	 */
	protected function isAllowed($method)
	{
		$data = \Nella\Security\Authorizator::parseAnnotations(get_called_class(), $method);
		
		$user = $this->getUser();
		if (isset($data['role']) && !$user->isInRole($data['role'])) {
			return FALSE;
		}
		if(!$data['resource'] && !$data['privilege']) {
			return TRUE;
		}
		
		return $user->isAllowed($data['resource'], $data['privilege']);
	}
	
	 /**
	  * Component factory. Delegates the creation of components to a createComponent<Name> method.
	  * @param  string
	  * @return \Nette\IComponent
	  */
	protected function createComponent($name)
	{
		$ucname = ucfirst($name);
		$method = 'createComponent' . $ucname;
		if ($ucname !== $name && method_exists($this, $method) && $this->getReflection()->getMethod($method)->getName() === $method) {
			if (!$this->isAllowed($method)) {
				throw new \Nette\Application\BadRequestException("You don't have permission for this '$name' component", 403);
			}
			
			return $this->$method($name);	
		}
	}
}