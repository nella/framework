<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application;

class BackendPresenterMock extends \Nella\Application\BackendPresenter
{
	/** @var \NellaTests\Mocks\User */
	private $userMock;
	/** @var string */
	public $lang = "en";
	
	public function __construct( Nette\IComponentContainer $parent = NULL, $name = NULL )
	{
		$this->userMock = new \NellaTests\Mocks\User;
		$this->setContext(new \Nette\Context);
		$this->getContext()->addService('Nella\Registry\GlobalComponentFactories', new \Nella\FreezableArray);
		parent::__construct($parent, $name);
	}
	
	public function startupMock()
	{
		return $this->startup();
	}
	
	public function isAllowedMock($method)
	{
		return $this->isAllowed($method);
	}
	
	public function createComponentMock($name)
	{
		return $this->createComponent($name);
	}
	
	public function getUser()
	{
		return $this->userMock;
	}
	
	public function setSignal($signal)
	{
		$ref = new \Nette\Reflection\PropertyReflection('Nette\Application\Presenter', 'signal');
		$ref->setAccessible(TRUE);
		$ref->setValue($this, $signal);
		$ref->setAccessible(TRUE);
	}
	
	public function getApplication()
	{
		$context = new \Nette\Context;
		$context->addService('Nette\Web\Session', new \Nette\Web\Session);
		$app = new \Nette\Application\Application;
		$app->setContext($context);
		return $app;
	}
	
	/**
	 * @allowed(resource=foo,privilege=bar)
	 */
	public function actionTest1() { }
	
	public function actionTest2() { }
	
	/**
	 * @allowed(resource=foo,privilege=bar)
	 */
	public function renderTest1() { }
	
	public function renderTest2() { }
	
	/**
	 * @allowed(resource=foo,privilege=bar)
	 */
	public function handleTest1() { }
	
	public function handleTest2() { }
	
	/**
	 * @allowed(resource=foo,privilege=bar)
	 */
	public function createComponentTest1() { }
	
	public function createComponentTest2() { }
}