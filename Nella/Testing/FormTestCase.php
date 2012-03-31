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
 * Base Nella Framework form test case for PHPUnit
 *
 * @author	Patrik Votoček
 */
abstract class FormTestCase extends TestCase
{
	/**
	 * @param \Nette\Application\UI\Form
	 * @param array
	 */
	protected function runForm(\Nette\Application\UI\Form $form, array $data)
	{
		$request = new \Nette\Application\Request('Default', 'POST', array('do' => 'test-submit'), $data);

		$presenter = new ControlPresenterMock($this->getContext(), $form);

		$presenter->run($request);
	}
}