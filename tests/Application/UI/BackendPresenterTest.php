<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application\UI;

use Nette\Reflection\ClassType;

class BackendPresenterTest extends \Nella\Testing\TestCase
{
	/** @var BackendPresenter\PresenterMock */
	private $presenter;

	public function setup()
	{
		parent::setup();
		$this->presenter = new BackendPresenter\PresenterMock;
		$this->presenter->setContext($this->getContext());
	}
	
	public function testLayout()
	{
		$this->presenter->startupMock();
		$this->assertEquals("backend", $this->presenter->layout);
	}
}

namespace NellaTests\Application\UI\BackendPresenter;

class PresenterMock extends \Nella\Application\UI\BackendPresenter
{
	public function startupMock()
	{
		return $this->startup();
	}
}