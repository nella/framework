<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
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