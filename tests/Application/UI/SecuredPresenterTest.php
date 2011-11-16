<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application\UI;

require_once __DIR__ . "/../../bootstrap.php";

use Nette\Reflection\ClassType;

class SecuredPresenterTest extends \Nella\Testing\TestCase
{
	/** @var SecuredPresenter\PresenterMock */
	private $presenter;

	public function setup()
	{
		parent::setup();
		$this->presenter = new SecuredPresenter\PresenterMock;
		$this->presenter->setContext($this->getContext());
	}

	/**
	 * @expectedException Nette\Application\ForbiddenRequestException
	 */
	public function testActionDeny()
	{
		$this->presenter->changeAction("test1");
		$this->presenter->checkRequirements(ClassType::from(__NAMESPACE__ . '\SecuredPresenter\PresenterMock'));
	}

	public function testActionAllow()
	{
		$this->presenter->changeAction("test2");
		$this->presenter->checkRequirements(ClassType::from(__NAMESPACE__ . '\SecuredPresenter\PresenterMock'));

		$this->assertFalse($this->presenter->isAllowedMock('actionTest1'));
		$this->assertTrue($this->presenter->isAllowedMock('actionTest2'));
	}

	/**
	 * @expectedException Nette\Application\ForbiddenRequestException
	 */
	public function testViewDeny()
	{
		$this->presenter->setView("test1");
		$this->presenter->checkRequirements(ClassType::from(__NAMESPACE__ . '\SecuredPresenter\PresenterMock'));
	}

	public function testViewAllow()
	{
		$this->presenter->setView("test2");
		$this->presenter->checkRequirements(ClassType::from(__NAMESPACE__ . '\SecuredPresenter\PresenterMock'));

		$this->assertFalse($this->presenter->isAllowedMock('renderTest1'));
		$this->assertTrue($this->presenter->isAllowedMock('renderTest2'));
		$this->assertFalse($this->presenter->isAllowedMock('renderTest3'));
	}

	/**
	 * @expectedException Nette\Application\ForbiddenRequestException
	 */
	public function testSignalDeny()
	{
		$this->presenter->setSignalMock("test1");
		$this->presenter->checkRequirements(ClassType::from(__NAMESPACE__ . '\SecuredPresenter\PresenterMock'));
	}

	public function testSignalAllow()
	{
		$this->presenter->setSignalMock("test2");
		$this->presenter->checkRequirements(ClassType::from(__NAMESPACE__ . '\SecuredPresenter\PresenterMock'));

		$this->assertFalse($this->presenter->isAllowedMock('handleTest1'));
		$this->assertTrue($this->presenter->isAllowedMock('handleTest2'));
	}

	/**
	 * @expectedException Nette\Application\ForbiddenRequestException
	 */
	public function testComponentDeny()
	{
		$this->presenter->createComponentMock('test1');
	}

	public function testComponentAllow()
	{
		$this->assertNull($this->presenter->createComponentMock('test2'));

		$this->assertFalse($this->presenter->isAllowedMock('createComponentTest1'));
		$this->assertTrue($this->presenter->isAllowedMock('createComponentTest2'));
	}

	public function testGlobalComponent()
	{
		$this->presenter->getContext()->getService('components')
			->addComponent('foo', function($parent, $name) { return "bar"; });

		$this->assertEquals("bar", $this->presenter->createComponentMock('foo'));
		$this->assertNull($this->presenter->createComponentMock('bar'));
	}
}

namespace NellaTests\Application\UI\SecuredPresenter;

class PresenterMock extends \Nella\Application\UI\SecuredPresenter
{
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

	public function setSignalMock($signal)
	{
		$ref = new \Nette\Reflection\Property('Nette\Application\UI\Presenter', 'signal');
		$ref->setAccessible(TRUE);
		$ref->setValue($this, $signal);
		$ref->setAccessible(TRUE);
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
	 * @allowed(role=baz)
	 */
	public function renderTest3() { }

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