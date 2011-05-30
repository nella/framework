<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Security;

use Nella\Forms\Form,
	Nette\Security\IAuthenticator;

/**
 * Sing in presenter
 *
 * @author	Patrik Votoček
 */
class FrontendPresenter extends \Nella\Application\UI\Presenter
{
	/**
	 * @param string
	 */
	protected function createComponentLoginForm($name)
	{
		new Forms\Login($this, $name);
	}
}
