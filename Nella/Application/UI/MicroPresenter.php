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
	 * Correctly terminates presenter
	 * 
	 * @return void
	 * @throws \Nette\Application\AbortException
	 */
	public function terminate()
	{
		throw new \Nette\Application\AbortException();
	}
}