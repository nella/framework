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