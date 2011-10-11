<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application\UI;

use Nella\Security\User;

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
