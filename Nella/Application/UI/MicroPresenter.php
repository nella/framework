<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace Nella\Application\UI;

/**
 * Simple presenter for reander via cllabacks
 *
 * @author	Patrik Votoček
 */
class MicroPresenter extends \NetteModule\MicroPresenter
{
	/**
	 * Correctly terminates presenter.
	 * @return void
	 * @throws Nette\Application\AbortException
	 */
	public function terminate()
	{
		if (func_num_args() !== 0) {
			trigger_error(__METHOD__ . ' is not intended to send a Application\Response; use sendResponse() instead.', E_USER_WARNING);
			$this->sendResponse(func_get_arg(0));
		}
		
		throw new \Nette\Application\AbortException();
	}
}