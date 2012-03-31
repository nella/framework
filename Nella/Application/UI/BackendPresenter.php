<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace Nella\Application\UI;

/**
 * Base backend presenter
 *
 * @author	Patrik Votoček
 */
abstract class BackendPresenter extends SecuredPresenter
{
	protected function startup()
	{
		parent::startup();

		$this->setLayout('backend');
	}
}
