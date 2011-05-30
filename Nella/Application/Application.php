<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace Nella\Application;

/**
 * Front Controller
 *
 * @author	Patrik Votoček
 */
class Application extends \Nette\Application\Application
{
	public function run()
	{
		if (PHP_SAPI == "cli") {
			$this->context->console->run();
		} else {
			parent::run();
		}
	}
}
