<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Testing;

/**
 * Presenter mock for component testing
 *
 * @author	Patrik Votoček
 */
class ControlPresenterMock extends \Nette\Application\UI\Presenter
{
	/** @var \Nette\ComponentModel\IComponent */
	private $component;

	/**
	 * @param \Nette\DI\Container
	 * @param \Nette\ComponentModel\IComponent
	 */
	public function __construct(\Nette\DI\Container $context, \Nette\ComponentModel\IComponent $component)
	{
		parent::__construct($context);
		$this->component = $component;
	}

	/**
	 * @return \Nette\ComponentModel\IComponent
	 */
	public function createComponentTest()
	{
		return $this->component;
	}

	protected function beforeRender()
	{
		$this->terminate();
	}
}